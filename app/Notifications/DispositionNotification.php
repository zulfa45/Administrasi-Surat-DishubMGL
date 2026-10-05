<?php

namespace App\Notifications;

use App\Models\Assignment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DispositionNotification extends Notification
{
    use Queueable;

    public Assignment $assignment;

    /**
     * Create a new notification instance.
     */
    public function __construct(Assignment $assignment)
    {
        $this->assignment = $assignment;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $letter = $this->assignment->incomingLetter;

        $link = route('surat-masuk.show', $letter->id);
        
        if ($notifiable->hasRole('kepala_bidang')) {
            $link = route('kabid.surat-masuk.show', $this->assignment->id);
        } elseif ($notifiable->hasRole('karyawan')) {
            $link = route('karyawan.tasks.show', $this->assignment->id);
        }

        return [
            'assignment_id'      => $this->assignment->id,
            'incoming_letter_id' => $letter ? $letter->id : null,
            'judul'              => 'Disposisi Surat Baru',
            'pesan'              => 'Anda menerima penugasan/pemberitahuan disposisi untuk surat: ' . ($letter ? $letter->nomor_surat : '-'),
            'perihal'            => $letter ? $letter->perihal : '-',
            'link'               => $link,
            'waktu'              => now()->translatedFormat('d M Y H:i'),
        ];
    }
}
