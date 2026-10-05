<?php

namespace App\Notifications;

use App\Models\Assignment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DispositionNotification extends Notification
{
    use Queueable;

    public Assignment $assignment;
    public ?string $customTitle;
    public ?string $customMessage;

    /**
     * Create a new notification instance.
     */
    public function __construct(Assignment $assignment, ?string $customTitle = null, ?string $customMessage = null)
    {
        $this->assignment = $assignment;
        $this->customTitle = $customTitle;
        $this->customMessage = $customMessage;
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

        $judul = $this->customTitle ?? 'Disposisi Surat Baru';
        $pesan = $this->customMessage ?? 'Anda menerima penugasan/pemberitahuan disposisi untuk surat: ' . ($letter ? $letter->nomor_surat : '-');

        return [
            'assignment_id'      => $this->assignment->id,
            'incoming_letter_id' => $letter ? $letter->id : null,
            'judul'              => $judul,
            'pesan'              => $pesan,
            'perihal'            => $letter ? $letter->perihal : '-',
            'link'               => $link,
            'waktu'              => now()->translatedFormat('d M Y H:i'),
        ];
    }
}
