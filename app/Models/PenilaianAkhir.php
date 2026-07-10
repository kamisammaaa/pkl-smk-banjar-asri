<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenilaianAkhir extends Model
{
    protected $table = 'penilaian_akhir';
    
    protected $fillable = [
        'pembimbing_id', 'siswa_user_id',
        'nilai_absensi', 'nilai_jurnal', 'nilai_sikap', 'nilai_akhir',
        'catatan_akhir', 'submitted_at'
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    public function pembimbing()
    {
        return $this->belongsTo(User::class, 'pembimbing_id');
    }

    public function siswa()
    {
        return $this->belongsTo(User::class, 'siswa_user_id');
    }

    public function getGradeAttribute(): string
    {
        $n = $this->nilai_akhir;
        if ($n >= 90) return 'A';
        if ($n >= 80) return 'B';
        if ($n >= 70) return 'C';
        if ($n >= 60) return 'D';
        return 'E';
    }
}