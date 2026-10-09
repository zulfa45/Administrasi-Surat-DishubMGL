<?php

namespace App\Http\Controllers\Kabid;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\IncomingLetter;
use App\Models\User;
use Illuminate\Http\Request;

class IncomingLetterController extends Controller
{
    /**
     * Tampilkan daftar surat masuk untuk bidang dengan pencarian dan filter status.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $deptId = $user->department_id;
        
        $baseQuery = Assignment::with(['incomingLetter', 'user'])
            ->where('department_id', $deptId);

        // Hitung total untuk status filter tabs
        $counts = [
            'all'                 => (clone $baseQuery)->count(),
            'belum_disposisi'     => (clone $baseQuery)->whereNull('user_id')->where('status', '!=', 'selesai')->count(),
            'sedang_proses'       => (clone $baseQuery)->whereNotNull('user_id')->whereIn('status', ['belum_dibaca', 'dibaca', 'dikerjakan', 'perlu_revisi'])->count(),
            'menunggu_verifikasi' => (clone $baseQuery)->where('status', 'menunggu_verifikasi_kabid')->count(),
            'selesai'             => (clone $baseQuery)->where('status', 'selesai')->count(),
        ];

        $query = clone $baseQuery;

        // Filter tab status
        if ($request->filled('status')) {
            $status = $request->status;
            if ($status === 'belum_disposisi') {
                $query->whereNull('user_id')->where('status', '!=', 'selesai');
            } elseif ($status === 'sedang_proses') {
                $query->whereNotNull('user_id')->whereIn('status', ['belum_dibaca', 'dibaca', 'dikerjakan', 'perlu_revisi']);
            } elseif ($status === 'menunggu_verifikasi') {
                $query->where('status', 'menunggu_verifikasi_kabid');
            } elseif ($status === 'selesai') {
                $query->where('status', 'selesai');
            }
        }

        // Pencarian kata kunci
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('incomingLetter', function ($lq) use ($search) {
                    $lq->where('nomor_surat', 'like', "%{$search}%")
                       ->orWhere('asal_surat', 'like', "%{$search}%")
                       ->orWhere('perihal', 'like', "%{$search}%")
                       ->orWhere('nomor_agenda', 'like', "%{$search}%");
                })->orWhereHas('user', function ($uq) use ($search) {
                    $uq->where('name', 'like', "%{$search}%");
                });
            });
        }

        $assignments = $query->latest('created_at')
            ->paginate(15)
            ->withQueryString();
            
        return view('kabid.surat-masuk.index', compact('assignments', 'counts'));
    }

    /**
     * Tampilkan detail surat dan form disposisi/verifikasi.
     */
    public function show(Assignment $assignment)
    {
        $user = auth()->user();
        
        // Pastikan assignment ini milik bidang dari kabid yang login
        if ($assignment->department_id !== $user->department_id) {
            abort(403, 'Unauthorized action.');
        }

        $assignment->load(['incomingLetter', 'user']);
        
        // Seluruh penugasan untuk surat ini di bidang yang sama
        $bidangAssignments = Assignment::with('user')
            ->where('incoming_letter_id', $assignment->incoming_letter_id)
            ->where('department_id', $user->department_id)
            ->get();

        // Karyawan dalam bidang yang sama
        $karyawan = User::where('department_id', $user->department_id)
            ->where('id', '!=', $user->id)
            ->whereHas('roles', function($q) {
                $q->where('name', 'karyawan');
            })->get();

        // Karyawan yang belum ditugaskan untuk surat ini
        $assignedUserIds = $bidangAssignments->pluck('user_id')->filter()->toArray();
        $unassignedKaryawan = $karyawan->whereNotIn('id', $assignedUserIds);

        return view('kabid.surat-masuk.show', compact('assignment', 'karyawan', 'bidangAssignments', 'unassignedKaryawan'));
    }

    /**
     * Disposisikan surat ke satu, beberapa, atau semua staf/karyawan.
     */
    public function disposisi(Request $request, Assignment $assignment)
    {
        $user = auth()->user();
        
        if ($assignment->department_id !== $user->department_id) {
            abort(403);
        }

        $tipeTindakan = $request->input('tipe_tindakan', 'karyawan');

        // Jika Kabid memilih opsi simpan sebagai arsip tanpa penugasan
        if ($tipeTindakan === 'arsip' || empty($request->input('user_ids'))) {
            if (!$assignment->user_id) {
                $assignment->update([
                    'user_id' => null,
                    'catatan_kabid' => $request->catatan_arsip ?? $request->catatan_kabid ?? 'Disimpan tanpa penugasan.',
                    'status' => 'selesai',
                    'tanggal_selesai' => now()->toDateString(),
                ]);
                $assignment->incomingLetter->update(['status' => 'selesai']);
                
                return back()->with('success', 'Surat berhasil disimpan (diarsipkan) tanpa penugasan staf.');
            }
        }

        $validated = $request->validate([
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => [
                'integer',
                function ($attribute, $value, $fail) use ($user) {
                    $karyawan = User::find($value);
                    if (!$karyawan || $karyawan->department_id !== $user->department_id) {
                        $fail('Staf yang dipilih tidak valid atau bukan dari bidang Anda.');
                    }
                },
            ],
            'catatan_kabid' => 'required|string',
            'deadline' => 'nullable|date|after_or_equal:today',
        ], [
            'user_ids.required' => 'Pilih minimal satu staf pelaksana.',
            'user_ids.min' => 'Pilih minimal satu staf pelaksana.',
            'catatan_kabid.required' => 'Instruksi / Catatan untuk staf wajib diisi.',
            'deadline.after_or_equal' => 'Batas waktu (deadline) tidak boleh berupa tanggal yang sudah lewat.',
        ]);

        $userIds = $validated['user_ids'];
        $count = count($userIds);

        // Jika assignment saat ini belum ditugaskan ke staf (user_id masih null)
        if (is_null($assignment->user_id)) {
            $firstUserId = array_shift($userIds);
            $assignment->update([
                'user_id' => $firstUserId,
                'catatan_kabid' => $validated['catatan_kabid'],
                'deadline' => $validated['deadline'],
                'status' => 'belum_dibaca',
            ]);

            $firstUser = User::find($firstUserId);
            if ($firstUser) {
                $firstUser->notify(new \App\Notifications\DispositionNotification(
                    $assignment,
                    'Tugas Disposisi Baru',
                    'Anda mendapat tugas dari Kepala Bidang untuk menindaklanjuti surat: ' . $assignment->incomingLetter->nomor_surat
                ));
            }
        }

        // Buat assignment baru untuk staf lainnya yang dipilih (bisa lebih dari 1 atau tambah staf)
        foreach ($userIds as $uid) {
            $existing = Assignment::where('incoming_letter_id', $assignment->incoming_letter_id)
                ->where('department_id', $assignment->department_id)
                ->where('user_id', $uid)
                ->first();

            if (!$existing) {
                $newAssignment = Assignment::create([
                    'incoming_letter_id' => $assignment->incoming_letter_id,
                    'department_id'      => $assignment->department_id,
                    'user_id'            => $uid,
                    'status'             => 'belum_dibaca',
                    'catatan'            => $assignment->catatan,
                    'catatan_kabid'      => $validated['catatan_kabid'],
                    'deadline'           => $validated['deadline'],
                    'tanggal_disposisi'  => $assignment->tanggal_disposisi ?? now()->toDateString(),
                ]);

                $staf = User::find($uid);
                if ($staf) {
                    $staf->notify(new \App\Notifications\DispositionNotification(
                        $newAssignment,
                        'Tugas Disposisi Baru',
                        'Anda mendapat tugas dari Kepala Bidang untuk menindaklanjuti surat: ' . $assignment->incomingLetter->nomor_surat
                    ));
                }
            }
        }

        $assignment->incomingLetter->update(['status' => 'didisposisikan']);

        return back()->with('success', "Surat berhasil didisposisikan ke {$count} staf.");
    }

    /**
     * Verifikasi hasil tindak lanjut karyawan.
     */
    public function verifikasi(Request $request, Assignment $assignment)
    {
        $user = auth()->user();
        
        if ($assignment->department_id !== $user->department_id) {
            abort(403);
        }

        $validated = $request->validate([
            'keputusan' => 'required|in:terima,revisi',
            'catatan_revisi' => 'required_if:keputusan,revisi|nullable|string',
        ]);

        if ($validated['keputusan'] === 'terima') {
            $assignment->update([
                'status' => 'selesai',
                'tanggal_selesai' => now()->toDateString(),
            ]);

            // Cek apakah seluruh penugasan untuk surat masuk ini sudah selesai
            $hasUnfinished = Assignment::where('incoming_letter_id', $assignment->incoming_letter_id)
                ->where('status', '!=', 'selesai')
                ->exists();

            if (!$hasUnfinished) {
                $assignment->incomingLetter->update(['status' => 'selesai']);
            }

            $msg = 'Hasil tindak lanjut disetujui dan status tugas staf dinyatakan Selesai.';
        } else {
            $assignment->update([
                'status' => 'perlu_revisi',
                'catatan_revisi' => $validated['catatan_revisi']
            ]);
            $assignment->incomingLetter->update(['status' => 'didisposisikan']);
            $msg = 'Hasil tindak lanjut dikembalikan ke karyawan untuk direvisi.';
        }

        // Notify Karyawan
        if ($assignment->user_id) {
            $karyawan = User::find($assignment->user_id);
            if ($karyawan) {
                $statusVerif = $validated['keputusan'] === 'terima' ? 'Disetujui' : 'Ditolak (Perlu Revisi)';
                $karyawan->notify(new \App\Notifications\DispositionNotification(
                    $assignment,
                    'Hasil Tindak Lanjut ' . $statusVerif,
                    'Laporan Anda untuk surat ' . $assignment->incomingLetter->nomor_surat . ' berstatus: ' . $statusVerif
                ));
            }
        }

        return back()->with('success', $msg);
    }

    /**
     * Membatalkan penugasan ke staf tertentu.
     */
    public function batalDisposisi(Assignment $assignment)
    {
        $user = auth()->user();
        
        if ($assignment->department_id !== $user->department_id) {
            abort(403);
        }

        // Jangan izinkan dibatalkan jika statusnya sudah dikerjakan/menunggu_verifikasi/selesai
        if (in_array($assignment->status, ['dikerjakan', 'menunggu_verifikasi_kabid', 'selesai'])) {
            return back()->with('error', 'Tugas ini sudah dalam proses atau selesai, tidak dapat dibatalkan.');
        }
        
        // Hapus file tindak lanjut jika ada (meskipun seharusnya tidak ada di status dibaca/belum_dibaca)
        if ($assignment->file_tindak_lanjut) {
            try {
                if (\Illuminate\Support\Facades\Storage::disk('google')->exists($assignment->file_tindak_lanjut)) {
                    \Illuminate\Support\Facades\Storage::disk('google')->delete($assignment->file_tindak_lanjut);
                }
            } catch (\Exception $e) {}
        }

        // Cek apakah ada assignment lain di bidang ini untuk surat yang sama
        $otherAssignment = Assignment::where('incoming_letter_id', $assignment->incoming_letter_id)
            ->where('department_id', $assignment->department_id)
            ->where('id', '!=', $assignment->id)
            ->first();

        if ($otherAssignment) {
            // Jika ada staf lain, hapus saja record assignment ini
            $assignment->delete();
            
            // Redirect ke halaman detail dari staf lain yang ada di surat ini
            return redirect()->route('kabid.surat-masuk.show', $otherAssignment->id)
                ->with('success', 'Penugasan staf berhasil dibatalkan.');
        } else {
            // Jika ini SATU-SATUNYA assignment untuk bidang ini, reset user_id menjadi null
            // agar surat kembali ke status "Menunggu Disposisi Kabid"
            $assignment->update([
                'user_id' => null,
                'status' => 'belum_dibaca',
                'catatan_kabid' => null,
                'deadline' => null,
                'catatan_tindak_lanjut' => null,
                'file_tindak_lanjut' => null,
            ]);
            
            return redirect()->route('kabid.surat-masuk.show', $assignment->id)
                ->with('success', 'Penugasan staf berhasil dibatalkan. Surat kembali ke antrean bidang.');
        }
    }
}
