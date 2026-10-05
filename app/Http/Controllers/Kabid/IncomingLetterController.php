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
     * Tampilkan daftar surat masuk untuk bidang.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        $assignments = Assignment::with(['incomingLetter', 'user'])
            ->where('department_id', $user->department_id)
            ->latest('created_at')
            ->paginate(15);
            
        return view('kabid.surat-masuk.index', compact('assignments'));
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
        
        // Karyawan dalam bidang yang sama
        $karyawan = User::where('department_id', $user->department_id)
            ->where('id', '!=', $user->id)
            ->whereHas('roles', function($q) {
                $q->where('name', 'karyawan');
            })->get();

        return view('kabid.surat-masuk.show', compact('assignment', 'karyawan'));
    }

    /**
     * Disposisikan surat ke karyawan.
     */
    public function disposisi(Request $request, Assignment $assignment)
    {
        $user = auth()->user();
        
        if ($assignment->department_id !== $user->department_id) {
            abort(403);
        }

        $validated = $request->validate([
            'user_id' => [
                'nullable',
                function ($attribute, $value, $fail) use ($user) {
                    if ($value) {
                        $karyawan = User::find($value);
                        if (!$karyawan || $karyawan->department_id !== $user->department_id) {
                            $fail('Karyawan yang dipilih tidak valid atau bukan dari bidang Anda.');
                        }
                    }
                },
            ],
            'catatan_kabid' => 'required_with:user_id|nullable|string',
            'deadline' => 'nullable|date|after_or_equal:today',
        ], [
            'catatan_kabid.required_with' => 'Instruksi wajib diisi jika Anda menugaskan ke karyawan.',
        ]);

        if (empty($validated['user_id'])) {
            // Cuma disimpan saja (Arsip / Selesai oleh Kabid)
            $assignment->update([
                'user_id' => null,
                'catatan_kabid' => $validated['catatan_kabid'] ?? 'Disimpan tanpa penugasan.',
                'status' => 'selesai',
                'tanggal_selesai' => now()->toDateString(),
            ]);
            $assignment->incomingLetter->update(['status' => 'selesai']);
            
            return back()->with('success', 'Surat berhasil disimpan (diarsipkan) tanpa ditugaskan.');
        }

        // Ditugaskan ke Karyawan
        $assignment->update([
            'user_id' => $validated['user_id'],
            'catatan_kabid' => $validated['catatan_kabid'],
            'deadline' => $validated['deadline'],
            'status' => 'belum_dibaca',
        ]);

        $assignment->incomingLetter->update(['status' => 'didisposisikan']);

        // Send notification to Karyawan
        $karyawan = User::find($validated['user_id']);
        if ($karyawan) {
            $karyawan->notify(new \App\Notifications\DispositionNotification(
                $assignment,
                'Tugas Disposisi Baru',
                'Anda mendapat tugas dari Kepala Bidang untuk menindaklanjuti surat: ' . $assignment->incomingLetter->nomor_surat
            ));
        }

        return back()->with('success', 'Surat berhasil didisposisikan ke karyawan.');
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
            $assignment->incomingLetter->update(['status' => 'selesai']);
            $msg = 'Hasil tindak lanjut disetujui dan surat dinyatakan Selesai.';
        } else {
            $assignment->update([
                'status' => 'perlu_revisi',
                'catatan_revisi' => $validated['catatan_revisi']
            ]);
            $assignment->incomingLetter->update(['status' => 'perlu_revisi']);
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
}
