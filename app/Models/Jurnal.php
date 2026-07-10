<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jurnal extends Model
{
    protected $fillable = [
        'siswa_user_id',
        'tanggal',
        'kegiatan',
        'kendala',
        'foto',
        'status',
        'nilai',              // ← Wajib ada!
        'catatan_revisi',     // ← Wajib ada!
    ];

    protected $casts = [
        'tanggal' => 'date',  // ← Agar format() bekerja di view
        'nilai' => 'integer',
    ];

    public function siswa()
    {
        return $this->belongsTo(User::class, 'siswa_user_id');
    }
}