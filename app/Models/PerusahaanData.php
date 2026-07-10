<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerusahaanData extends Model
{
    protected $fillable = [
        'siswa_user_id',
        'nama_perusahaan',
        'alamat_pembimbing',
        'nama_pembimbing',
        'ttl_pembimbing',
        'no_telp',
        'is_verified'
    ];

    public function siswa()
    {
        return $this->belongsTo(User::class, 'siswa_user_id');
    }
}
