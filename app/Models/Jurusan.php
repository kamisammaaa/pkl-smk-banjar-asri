<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jurusan extends Model
{
    // 🔥 Paksa nama tabel singular sesuai migration
    protected $table = 'jurusan';
    protected $fillable = ['nama'];
}