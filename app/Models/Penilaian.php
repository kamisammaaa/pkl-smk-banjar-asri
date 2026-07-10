<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penilaian extends Model
{
protected $table = 'penilaians';
protected $fillable = ['pembimbing_id','siswa_user_id','kategori','nilai','keterangan'];
public function pembimbing() { return $this->belongsTo(User::class, 'pembimbing_id'); }
public function siswa() { return $this->belongsTo(User::class, 'siswa_user_id'); }
}
