<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Halaman daftar seluruh notifikasi pengguna.
     */
    public function index()
    {
        $notifications = auth()->user()->notifications()->latest()->paginate(15);
        return view('notifications.index', compact('notifications'));
    }

    /**
     * Tandai satu notifikasi sebagai sudah dibaca dan alihkan ke tujuan.
     */
    public function read($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        
        if (is_null($notification->read_at)) {
            $notification->markAsRead();
        }

        $data = $notification->data;

        // Arahkan ke task karyawan jika user adalah karyawan
        if (auth()->user()->hasRole('karyawan') && !empty($data['assignment_id'])) {
            return redirect()->route('karyawan.tasks.show', $data['assignment_id']);
        }
        
        // Arahkan ke detail surat bidang jika user adalah kepala_bidang
        if (auth()->user()->hasRole('kepala_bidang') && !empty($data['assignment_id'])) {
            return redirect()->route('kabid.surat-masuk.show', $data['assignment_id']);
        }

        // Arahkan ke detail surat masuk
        if (!empty($data['incoming_letter_id']) && auth()->user()->hasAnyRole(['admin', 'staf-loket'])) {
            return redirect()->route('surat-masuk.show', $data['incoming_letter_id']);
        }

        if (!empty($data['link'])) {
            return redirect($data['link']);
        }

        return back();
    }

    /**
     * Tandai seluruh notifikasi yang belum dibaca sebagai sudah dibaca.
     */
    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'Semua notifikasi berhasil ditandai sudah dibaca.');
    }

    /**
     * Cek notifikasi terbaru secara realtime (AJAX polling).
     */
    public function check()
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['count' => 0]);
        }

        $unreadCount = $user->unreadNotifications()->count();
        $latest = $user->unreadNotifications()->latest()->first();

        return response()->json([
            'count' => $unreadCount,
            'latest_id' => $latest ? $latest->id : null,
            'latest_title' => $latest ? ($latest->data['judul'] ?? 'Notifikasi Baru') : null,
        ]);
    }
}
