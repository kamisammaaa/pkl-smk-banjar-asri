<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kunjungan extends Model
{
    protected $fillable = [
        'pembimbing_id',
        'siswa_user_id',
        'perusahaan_id',
        'tanggal',
        'waktu',
        'catatan',
        'catatan_rencana',
        'foto',
        'status',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'waktu' => 'datetime:H:i',
    ];

    public function pembimbing()
    {
        return $this->belongsTo(User::class, 'pembimbing_id');
    }

    public function siswa()
    {
        return $this->belongsTo(User::class, 'siswa_user_id');
    }

    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class, 'perusahaan_id');
    }
}