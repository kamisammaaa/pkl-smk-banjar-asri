<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiswaProfile extends Model
{
    protected $table = 'siswa_profiles';
    
    protected $fillable = [
        'user_id',
        'nis',
        'jurusan_id',
        'kelas',              // ← Pastikan ini ada!
        'perusahaan_id',
        'pembimbing_id',
    ];

    protected $casts = [
        // Tambahkan casts jika perlu
    ];

    public function user() { return $this->belongsTo(User::class, 'user_id'); }
    public function jurusan() { return $this->belongsTo(Jurusan::class); }
    public function perusahaan() { return $this->belongsTo(Perusahaan::class); }
    public function pembimbing() { return $this->belongsTo(User::class, 'pembimbing_id'); }
}