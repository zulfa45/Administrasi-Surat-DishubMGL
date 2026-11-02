<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DirectTaskController extends Controller
{
    /**
     * Tampilkan daftar monitoring seluruh tugas langsung dari seluruh Kepala Bidang ke staf.
     */
    public function index(Request $request)
    {
        $baseQuery = Assignment::with(['user', 'creator', 'department'])
            ->whereNull('incoming_letter_id');

        // Filter per Department / Bidang jika dipilih
        if ($request->filled('department_id')) {
            $baseQuery->where('department_id', $request->department_id);
        }

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
                  })
                  ->orWhereHas('creator', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('department', function ($dq) use ($search) {
                      $dq->where('name', 'like', "%{$search}%");
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

        $departments = Department::orderBy('name', 'asc')->get();

        return view('admin.tugas.index', compact('tasks', 'counts', 'departments'));
    }

    /**
     * Tampilkan detail tugas langsung untuk pemantauan Admin.
     */
    public function show(Assignment $task)
    {
        if (!is_null($task->incoming_letter_id)) {
            abort(404, 'Tugas ini bukan tugas langsung mandiri.');
        }

        $task->load(['user', 'creator', 'department']);

        return view('admin.tugas.show', compact('task'));
    }

    /**
     * Hapus / Batalkan tugas langsung oleh Admin.
     */
    public function destroy(Assignment $task)
    {
        if (!is_null($task->incoming_letter_id)) {
            abort(403, 'Aksi tidak diizinkan.');
        }

        // Hapus file tugas jika ada
        if ($task->file_tugas) {
            try {
                if (Storage::disk('google')->exists($task->file_tugas)) {
                    Storage::disk('google')->delete($task->file_tugas);
                } elseif (Storage::disk('s3')->exists($task->file_tugas)) {
                    Storage::disk('s3')->delete($task->file_tugas);
                } elseif (Storage::disk('public')->exists($task->file_tugas)) {
                    Storage::disk('public')->delete($task->file_tugas);
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
                } elseif (Storage::disk('public')->exists($task->file_tindak_lanjut)) {
                    Storage::disk('public')->delete($task->file_tindak_lanjut);
                }
            } catch (\Exception $e) {
                // Ignore failure
            }
        }

        $task->delete();

        return redirect()->route('admin.tugas.index')
            ->with('success', 'Tugas langsung berhasil dihapus oleh Admin.');
    }
}
