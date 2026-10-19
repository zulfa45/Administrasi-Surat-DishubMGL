<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'incoming_letter_id',
        'user_id',
        'department_id',
        'status',
        'catatan',
        'catatan_kabid',
        'catatan_tindak_lanjut',
        'file_tindak_lanjut',
        'catatan_revisi',
        'tanggal_disposisi',
        'deadline',
        'tanggal_selesai',
        'judul_tugas',
        'sifat',
        'file_tugas',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal_disposisi' => 'date',
            'deadline'          => 'date',
            'tanggal_selesai'   => 'date',
        ];
    }

    /**
     * Cek apakah ini tugas langsung dari Kabid (tanpa surat masuk).
     */
    public function isDirectTask(): bool
    {
        return is_null($this->incoming_letter_id);
    }

    /**
     * Accessor untuk judul atau perihal tugas.
     */
    public function getJudulAttribute(): string
    {
        return $this->judul_tugas ?: ($this->incomingLetter?->perihal ?? 'Tanpa Perihal/Judul');
    }

    /**
     * Accessor untuk sifat / prioritas tugas.
     */
    public function getSifatTugasAttribute(): string
    {
        return $this->sifat ?: ($this->incomingLetter?->sifat ?? 'Biasa');
    }

    /**
     * Accessor untuk nomor identitas referensi tugas.
     */
    public function getNomorReferensiAttribute(): string
    {
        if ($this->incomingLetter) {
            return $this->incomingLetter->nomor_agenda ? '#' . $this->incomingLetter->nomor_agenda : $this->incomingLetter->nomor_surat;
        }

        return 'TUGAS-DIR-' . str_pad($this->id, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Surat masuk yang didisposisikan (jika ada).
     */
    public function incomingLetter()
    {
        return $this->belongsTo(IncomingLetter::class);
    }

    /**
     * Pegawai penerima disposisi / tugas.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Pembuat tugas langsung (misal Kepala Bidang).
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Department atau bagian penerima disposisi / tugas.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
