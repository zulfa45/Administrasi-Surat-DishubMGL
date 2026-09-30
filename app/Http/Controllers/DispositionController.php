<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Department;
use App\Models\IncomingLetter;
use App\Models\User;
use App\Notifications\DispositionNotification;
use Illuminate\Http\Request;

class DispositionController extends Controller
{
    /**
     * Tampilkan form untuk membuat disposisi baru pada surat tertentu.
     */
    public function create(IncomingLetter $surat_masuk)
    {
        $departments = Department::where('status', 'active')->orderBy('name')->get();
        $users = User::where('status', 'active')
            ->whereDoesntHave('roles', function ($q) {
                $q->where('name', 'admin');
            })
            ->with('department')
            ->orderBy('name')
            ->get();

        // Jika tidak ada filter role (misal seeder belum jalan), fallback ambil semua user aktif
        if ($users->isEmpty()) {
            $users = User::where('status', 'active')->with('department')->orderBy('name')->get();
        }

        return view('surat-masuk.disposisi', [
            'letter'      => $surat_masuk,
            'departments' => $departments,
            'users'       => $users,
        ]);
    }

    /**
     * Simpan disposisi baru, update status surat, dan kirim notifikasi.
     */
    public function store(Request $request, IncomingLetter $surat_masuk)
    {
        $validated = $request->validate([
            'target_type'       => 'required|in:user,department',
            'user_id'           => 'required_if:target_type,user|nullable|exists:users,id',
            'department_id'     => 'required_if:target_type,department|nullable|exists:departments,id',
            'catatan'           => 'required|string|max:1000',
            'tanggal_disposisi' => 'required|date',
        ], [
            'target_type.required'       => 'Pilih tujuan disposisi (Individu Pegawai atau Bagian/Department).',
            'user_id.required_if'        => 'Pilih pegawai penerima disposisi.',
            'department_id.required_if'  => 'Pilih department penerima disposisi.',
            'catatan.required'           => 'Instruksi / Catatan disposisi wajib diisi.',
            'tanggal_disposisi.required' => 'Tanggal disposisi wajib diisi.',
        ]);

        $userId = $request->target_type === 'user' ? $request->user_id : null;
        $deptId = $request->target_type === 'department' ? $request->department_id : null;

        // Jika dipilih user, otomatis hubungkan ke department user jika ada
        if ($userId && !$deptId) {
            $selectedUser = User::find($userId);
            if ($selectedUser && $selectedUser->department_id) {
                $deptId = $selectedUser->department_id;
            }
        }

        // Buat data assignment (Tahap U-07)
        $assignment = Assignment::create([
            'incoming_letter_id' => $surat_masuk->id,
            'user_id'            => $userId,
            'department_id'      => $deptId,
            'status'             => 'belum_dibaca',
            'catatan'            => $validated['catatan'],
            'tanggal_disposisi'  => $validated['tanggal_disposisi'],
        ]);

        // Perbarui status surat masuk jika masih 'baru'
        if ($surat_masuk->status === 'baru') {
            $surat_masuk->update(['status' => 'didistribusikan']);
        }

        // Kirim Notifikasi Database (Tahap U-12)
        if ($userId) {
            $recipient = User::find($userId);
            if ($recipient) {
                $recipient->notify(new DispositionNotification($assignment));
            }
        } elseif ($deptId) {
            // Notifikasi ke seluruh anggota department tersebut
            $deptMembers = User::where('department_id', $deptId)->get();
            foreach ($deptMembers as $member) {
                $member->notify(new DispositionNotification($assignment));
            }
        }

        return redirect()->route('surat-masuk.show', $surat_masuk)
            ->with('success', 'Disposisi berhasil dibuat dan notifikasi telah dikirimkan kepada penerima.');
    }

    /**
     * Hapus / batalkan disposisi surat (Admin/Staf-Loket).
     */
    public function destroy(Assignment $assignment)
    {
        $letter = $assignment->incomingLetter;
        $assignment->delete();

        // Jika tidak ada lagi assignment dan status masih didistribusikan, kembalikan ke baru
        if ($letter && $letter->assignments()->count() === 0 && $letter->status === 'didistribusikan') {
            $letter->update(['status' => 'baru']);
        }

        return back()->with('success', 'Disposisi berhasil dibatalkan/dihapus.');
    }

    /**
     * Perbarui status tindak lanjut assignment (Tahap U-08 & U-11).
     * Alur: belum_dibaca -> dibaca -> dikerjakan -> selesai
     */
    public function updateStatus(Request $request, Assignment $assignment)
    {
        $validated = $request->validate([
            'status'                => 'required|in:belum_dibaca,dibaca,dikerjakan,selesai',
            'catatan_tindak_lanjut' => 'nullable|string|max:1000',
        ]);

        $updateData = [
            'status' => $validated['status'],
        ];

        if ($request->filled('catatan_tindak_lanjut')) {
            $updateData['catatan_tindak_lanjut'] = $validated['catatan_tindak_lanjut'];
        }

        // Jika selesai, catat tanggal_selesai
        if ($validated['status'] === 'selesai') {
            $updateData['tanggal_selesai'] = now()->toDateString();
        } else {
            $updateData['tanggal_selesai'] = null;
        }

        $assignment->update($updateData);

        // Update status surat masuk induk
        $letter = $assignment->incomingLetter;
        if ($letter) {
            if ($validated['status'] === 'dikerjakan' && $letter->status !== 'selesai') {
                $letter->update(['status' => 'dalam_tindak_lanjut']);
            }

            // Jika semua assignment selesai, ubah status surat menjadi selesai
            $totalAssignments = $letter->assignments()->count();
            $completedAssignments = $letter->assignments()->where('status', 'selesai')->count();

            if ($totalAssignments > 0 && $totalAssignments === $completedAssignments) {
                $letter->update(['status' => 'selesai']);
            } elseif ($letter->status === 'selesai' && $completedAssignments < $totalAssignments) {
                $letter->update(['status' => 'dalam_tindak_lanjut']);
            }
        }

        return back()->with('success', 'Status tindak lanjut disposisi berhasil diperbarui.');
    }
}
