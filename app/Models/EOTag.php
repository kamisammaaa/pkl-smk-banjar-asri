<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EOTag extends Model
{
    // 🔥 PAKSA NAMA TABEL SESUAI MIGRATION
    protected $table = 'e_otags';
    
    protected $fillable = [
        'siswa_user_id',
        'token',
        'check_in_at',
        'ip_address',
        'status'
    ];

    protected $casts = [
        'check_in_at' => 'datetime',
    ];

    // 🔗 Relasi ke User (siswa)
    public function siswa()
    {
        return $this->belongsTo(User::class, 'siswa_user_id');
    }
}