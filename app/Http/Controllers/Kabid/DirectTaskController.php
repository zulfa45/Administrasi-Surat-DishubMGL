<?php

namespace App\Http\Controllers\Kabid;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DirectTaskController extends Controller
{
    /**
     * Tampilkan daftar seluruh tugas mandiri / langsung dari Kepala Bidang ke staf sebidang.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $deptId = $user->department_id;

        $baseQuery = Assignment::with(['user', 'creator'])
            ->whereNull('incoming_letter_id')
            ->where('department_id', $deptId);

        // Hitung total tab status
        $counts = [
            'all'                 => (clone $baseQuery)->count(),
            'baru'                => (clone $baseQuery)->whereIn('status', ['belum_dibaca', 'dibaca'])->count(),
            'dikerjakan'          => (clone $baseQuery)->where('status', 'dikerjakan')->count(),
            'menunggu_verifikasi' => (clone $baseQuery)->where('status', 'menunggu_verifikasi_kabid')->count(),
            'perlu_revisi'        => (clone $baseQuery)->where('status', 'perlu_revisi')->count(),
            'selesai'             => (clone $baseQuery)->where('status', 'selesai')->count(),
        ];

        $query = clone $baseQuery;

        // Filter status tab
        if ($request->filled('status')) {
            $status = $request->status;
            if ($status === 'baru') {
                $query->whereIn('status', ['belum_dibaca', 'dibaca']);
            } elseif ($status === 'dikerjakan') {
                $query->where('status', 'dikerjakan');
            } elseif ($status === 'menunggu_verifikasi') {
                $query->where('status', 'menunggu_verifikasi_kabid');
            } elseif ($status === 'perlu_revisi') {
                $query->where('status', 'perlu_revisi');
            } elseif ($status === 'selesai') {
                $query->where('status', 'selesai');
            }
        }

        // Pencarian kata kunci
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul_tugas', 'like', "%{$search}%")
                  ->orWhere('catatan_kabid', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter Sifat Tugas
        if ($request->filled('sifat')) {
            $query->where('sifat', $request->sifat);
        }

        $tasks = $query->latest('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('kabid.tugas.index', compact('tasks', 'counts'));
    }

    /**
     * Tampilkan formulir pembuatan tugas langsung untuk staf sebidang.
     */
    public function create(Request $request)
    {
        $user = auth()->user();

        // Karyawan aktif di bidang yang sama
        $members = User::where('department_id', $user->department_id)
            ->where('id', '!=', $user->id)
            ->whereHas('roles', function ($q) {
                $q->where('name', 'karyawan');
            })
            ->orderBy('name')
            ->get();

        $selectedUserId = $request->query('user_id');

        return view('kabid.tugas.create', compact('members', 'selectedUserId'));
    }

    /**
     * Simpan penugasan langsung baru untuk satu atau beberapa staf sebidang.
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'judul_tugas'        => 'required|string|max:255',
            'sifat'              => 'required|in:Biasa,Penting,Segera,Sangat Segera,Rahasia',
            'deadline'           => 'nullable|date|after_or_equal:today',
            'catatan_kabid'      => 'required|string',
            'user_ids'           => 'required|array|min:1',
            'user_ids.*'         => [
                'integer',
                function ($attribute, $value, $fail) use ($user) {
                    $staff = User::find($value);
                    if (!$staff || $staff->department_id !== $user->department_id) {
                        $fail('Staf yang dipilih tidak valid atau bukan berasal dari bidang Anda.');
                    }
                },
            ],
            'file_tugas'         => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx|max:10240',
            'nama_file_tugas'    => 'nullable|string|max:255',
        ], [
            'judul_tugas.required'   => 'Judul atau perihal tugas wajib diisi.',
            'catatan_kabid.required' => 'Instruksi / Catatan arahan wajib diisi.',
            'user_ids.required'      => 'Pilih minimal satu staf pelaksana tugas.',
            'user_ids.min'           => 'Pilih minimal satu staf pelaksana tugas.',
            'deadline.after_or_equal'=> 'Batas waktu (deadline) tidak boleh tanggal yang sudah lewat.',
        ]);

        // Upload lampiran file petunjuk dari kabid jika ada
        $filePath = null;
        if ($request->hasFile('file_tugas')) {
            $file = $request->file('file_tugas');
            $baseName = $request->filled('nama_file_tugas')
                ? Str::slug($request->nama_file_tugas)
                : Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
            $filename = time() . '_petunjuk_' . $baseName . '.' . $file->getClientOriginalExtension();
            $path = 'tugas-internal/' . $filename;

            try {
                Storage::disk('google')->put($path, file_get_contents($file->getRealPath()));
                $filePath = $path;
            } catch (\Exception $e) {
                try {
                    Storage::disk('s3')->put($path, file_get_contents($file->getRealPath()));
                    $filePath = $path;
                } catch (\Exception $s3Error) {
                    $filePath = $file->store('tugas-internal', 'public');
                }
            }
        }

        $userIds = $validated['user_ids'];
        $count = count($userIds);

        foreach ($userIds as $uid) {
            $assignment = Assignment::create([
                'incoming_letter_id' => null,
                'department_id'      => $user->department_id,
                'user_id'            => $uid,
                'created_by'         => $user->id,
                'judul_tugas'        => $validated['judul_tugas'],
                'sifat'              => $validated['sifat'],
                'catatan_kabid'      => $validated['catatan_kabid'],
                'file_tugas'         => $filePath,
                'deadline'           => $validated['deadline'],
                'tanggal_disposisi'  => now()->toDateString(),
                'status'             => 'belum_dibaca',
            ]);

            $staf = User::find($uid);
            if ($staf) {
                $staf->notify(new \App\Notifications\DispositionNotification(
                    $assignment,
                    'Tugas Baru dari Kepala Bidang',
                    'Anda menerima tugas langsung dari Kepala Bidang: ' . $assignment->judul_tugas
                ));
            }
        }

        return redirect()->route('kabid.tugas.index')
            ->with('success', "Tugas '{$validated['judul_tugas']}' berhasil dibuat dan ditugaskan ke {$count} staf.");
    }

    /**
     * Tampilkan detail tugas langsung dan status tindak lanjut/verifikasi.
     */
    public function show(Assignment $task)
    {
        $user = auth()->user();

        if ($task->department_id !== $user->department_id) {
            abort(403, 'Anda tidak memiliki akses ke tugas ini.');
        }

        $task->load(['user', 'creator', 'department']);

        return view('kabid.tugas.show', compact('task'));
    }

    /**
     * Verifikasi hasil tindak lanjut staf untuk tugas langsung.
     */
    public function verifikasi(Request $request, Assignment $task)
    {
        $user = auth()->user();

        if ($task->department_id !== $user->department_id) {
            abort(403);
        }

        $validated = $request->validate([
            'keputusan'      => 'required|in:terima,revisi',
            'catatan_revisi' => 'required_if:keputusan,revisi|nullable|string',
        ], [
            'catatan_revisi.required_if' => 'Catatan revisi wajib diisi jika Anda meminta revisi hasil kerja.',
        ]);

        if ($validated['keputusan'] === 'terima') {
            $task->update([
                'status'          => 'selesai',
                'tanggal_selesai' => now()->toDateString(),
            ]);

            if ($task->user) {
                $task->user->notify(new \App\Notifications\DispositionNotification(
                    $task,
                    'Laporan Tugas Disetujui',
                    'Hasil tindak lanjut Anda untuk tugas "' . $task->judul . '" telah disetujui oleh Kepala Bidang.'
                ));
            }

            return back()->with('success', 'Hasil tindak lanjut berhasil diverifikasi dan tugas dinyatakan SELESAI.');
        }

        // Jika revisi
        $task->update([
            'status'         => 'perlu_revisi',
            'catatan_revisi' => $validated['catatan_revisi'],
        ]);

        if ($task->user) {
            $task->user->notify(new \App\Notifications\DispositionNotification(
                $task,
                'Tugas Memerlukan Revisi',
                'Kepala Bidang meminta perbaikan untuk tugas "' . $task->judul . '". Catatan: ' . $validated['catatan_revisi']
            ));
        }

        return back()->with('success', 'Catatan revisi berhasil dikirim kepada staf pelaksana.');
    }

    /**
     * Batalkan / Hapus penugasan langsung.
     */
    public function destroy(Assignment $task)
    {
        $user = auth()->user();

        if ($task->department_id !== $user->department_id || !is_null($task->incoming_letter_id)) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        // Hapus file tugas jika ada
        if ($task->file_tugas) {
            try {
                if (Storage::disk('google')->exists($task->file_tugas)) {
                    Storage::disk('google')->delete($task->file_tugas);
                } elseif (Storage::disk('s3')->exists($task->file_tugas)) {
                    Storage::disk('s3')->delete($task->file_tugas);
                }
            } catch (\Exception $e) {
                // Ignore failure
            }
        }

        // Hapus file hasil jika ada
        if ($task->file_tindak_lanjut) {
            try {
                if (Storage::disk('google')->exists($task->file_tindak_lanjut)) {
                    Storage::disk('google')->delete($task->file_tindak_lanjut);
                } elseif (Storage::disk('s3')->exists($task->file_tindak_lanjut)) {
                    Storage::disk('s3')->delete($task->file_tindak_lanjut);
                }
            } catch (\Exception $e) {
                // Ignore failure
            }
        }

        $task->delete();

        return redirect()->route('kabid.tugas.index')
            ->with('success', 'Tugas langsung berhasil dihapus.');
    }

    /**
     * Pratinjau / download file petunjuk tugas dari Kabid.
     */
    public function previewFile(Assignment $task)
    {
        if (!$task->file_tugas) {
            abort(404, 'File petunjuk tidak ditemukan.');
        }

        if (Storage::disk('google')->exists($task->file_tugas)) {
            return Storage::disk('google')->response($task->file_tugas);
        }

        if (Storage::disk('s3')->exists($task->file_tugas)) {
            return redirect(Storage::disk('s3')->url($task->file_tugas));
        }

        if (Storage::disk('public')->exists($task->file_tugas)) {
            return Storage::disk('public')->response($task->file_tugas);
        }

        abort(404, 'File petunjuk tidak ditemukan di penyimpanan.');
    }
}
