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
        'catatan_tindak_lanjut',
        'tanggal_disposisi',
        'deadline',
        'tanggal_selesai',
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
     * Surat masuk yang didisposisikan.
     */
    public function incomingLetter()
    {
        return $this->belongsTo(IncomingLetter::class);
    }

    /**
     * Pegawai penerima disposisi.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Department atau bagian penerima disposisi.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
