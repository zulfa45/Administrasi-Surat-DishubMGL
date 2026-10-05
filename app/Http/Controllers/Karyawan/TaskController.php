<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Helper query untuk mengambil tugas yang ditujukan ke karyawan atau bagiannya.
     */
    protected function getBaseTaskQuery()
    {
        $user = auth()->user();

        return Assignment::with(['incomingLetter.creator', 'department'])
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id);
                if ($user->department_id) {
                    $query->orWhere('department_id', $user->department_id);
                }
            });
    }

    /**
     * Dashboard Karyawan (Tahap U-09).
     * Menampilkan statistik tugas dan daftar tugas terbaru.
     */
    public function dashboard()
    {
        $baseQuery = $this->getBaseTaskQuery();

        $totalTugas       = (clone $baseQuery)->count();
        $tugasBaru        = (clone $baseQuery)->whereIn('status', ['belum_dibaca', 'dibaca'])->count();
        $sedangDikerjakan = (clone $baseQuery)->where('status', 'dikerjakan')->count();
        $tugasSelesai     = (clone $baseQuery)->where('status', 'selesai')->count();

        // Tugas dengan batas waktu mendesak (<= 2 hari) atau surat bersifat Segera/Penting
        $tugasMendesak = (clone $baseQuery)
            ->where('status', '!=', 'selesai')
            ->where(function ($q) {
                $q->where(function ($sub) {
                    $sub->whereNotNull('deadline')
                        ->where('deadline', '<=', now()->addDays(2)->toDateString());
                })->orWhereHas('incomingLetter', function ($lq) {
                    $lq->whereIn('sifat', ['Sangat Segera', 'Segera', 'Penting']);
                });
            })
            ->count();

        $recentTasks = (clone $baseQuery)
            ->latest('tanggal_disposisi')
            ->latest('id')
            ->take(6)
            ->get();

        return view('karyawan.dashboard', compact(
            'totalTugas',
            'tugasBaru',
            'sedangDikerjakan',
            'tugasSelesai',
            'tugasMendesak',
            'recentTasks'
        ));
    }

    /**
     * Halaman Daftar Seluruh Tugas Karyawan (Tahap U-09).
     */
    public function index(Request $request)
    {
        $baseQuery = $this->getBaseTaskQuery();

        $counts = [
            'all'        => (clone $baseQuery)->count(),
            'baru'       => (clone $baseQuery)->whereIn('status', ['belum_dibaca', 'dibaca'])->count(),
            'dikerjakan' => (clone $baseQuery)->where('status', 'dikerjakan')->count(),
            'selesai'    => (clone $baseQuery)->where('status', 'selesai')->count(),
        ];

        $query = clone $baseQuery;

        // Filter kata kunci pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('incomingLetter', function ($q) use ($search) {
                $q->where('nomor_surat', 'like', "%{$search}%")
                  ->orWhere('nomor_agenda', 'like', "%{$search}%")
                  ->orWhere('asal_surat', 'like', "%{$search}%")
                  ->orWhere('perihal', 'like', "%{$search}%");
            });
        }

        // Filter status
        if ($request->filled('status')) {
            if ($request->status === 'baru') {
                $query->whereIn('status', ['belum_dibaca', 'dibaca']);
            } else {
                $query->where('status', $request->status);
            }
        }

        // Filter sifat surat
        if ($request->filled('sifat')) {
            $query->whereHas('incomingLetter', function ($q) use ($request) {
                $q->where('sifat', $request->sifat);
            });
        }

        $tasks = $query->latest('tanggal_disposisi')
                       ->latest('id')
                       ->paginate(10)
                       ->withQueryString();

        return view('karyawan.tasks.index', compact('tasks', 'counts'));
    }

    /**
     * Detail Tugas Karyawan (Tahap U-10).
     */
    public function show(Assignment $task)
    {
        $user = auth()->user();

        // Otorisasi: Karyawan hanya boleh melihat tugasnya sendiri atau tugas bagiannya
        if ($task->user_id !== $user->id && $task->department_id !== $user->department_id) {
            abort(403, 'Anda tidak memiliki akses ke tugas disposisi ini.');
        }

        // Jika status masih belum_dibaca, otomatis tandai 'dibaca' (Tahap U-08)
        if ($task->status === 'belum_dibaca') {
            $task->update(['status' => 'dibaca']);
        }

        $task->load(['incomingLetter.creator', 'department', 'incomingLetter.assignments.user']);

        return view('karyawan.tasks.show', [
            'task'   => $task,
            'letter' => $task->incomingLetter,
        ]);
    }

    /**
     * Update Status & Catatan Tindak Lanjut oleh Karyawan (Tahap U-10 & U-11).
     * Action: Tandai Dibaca, Mulai Kerjakan, Tandai Selesai + Tanggapan
     */
    public function updateStatus(Request $request, Assignment $task)
    {
        $user = auth()->user();

        if ($task->user_id !== $user->id && $task->department_id !== $user->department_id) {
            abort(403, 'Anda tidak memiliki akses ke tugas disposisi ini.');
        }

        $validated = $request->validate([
            'status'                => 'required|in:dibaca,dikerjakan,menunggu_verifikasi_kabid',
            'catatan_tindak_lanjut' => 'required_if:status,menunggu_verifikasi_kabid|nullable|string',
            'file_tindak_lanjut'    => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
        ], [
            'catatan_tindak_lanjut.required_if' => 'Laporan atau keterangan wajib diisi saat menyelesaikan tugas.'
        ]);

        $updateData = ['status' => $validated['status']];

        if ($request->filled('catatan_tindak_lanjut')) {
            $updateData['catatan_tindak_lanjut'] = $validated['catatan_tindak_lanjut'];
        }
        
        if ($request->hasFile('file_tindak_lanjut')) {
            $file = $request->file('file_tindak_lanjut');
            $filename = time() . '_hasil_' . \Illuminate\Support\Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            $path = 'tindak-lanjut/' . $filename;
            \Illuminate\Support\Facades\Storage::disk('google')->put($path, file_get_contents($file->getRealPath()));
            $updateData['file_tindak_lanjut'] = $path;
        }

        $task->update($updateData);

        // Update status surat induk
        $letter = $task->incomingLetter;
        if ($letter) {
            if ($validated['status'] === 'dikerjakan' && $letter->status !== 'selesai' && $letter->status !== 'menunggu_verifikasi_kabid') {
                $letter->update(['status' => 'dalam_tindak_lanjut']);
            } elseif ($validated['status'] === 'menunggu_verifikasi_kabid') {
                $letter->update(['status' => 'menunggu_verifikasi_kabid']);
            }
        }

        return back()->with('success', 'Status dan laporan tindak lanjut berhasil diperbarui.');
    }
}
