<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeriodePKL extends Model
{
    protected $table = 'periode_pkls';
    
    // ✅ PASTIKAN is_active ADA DI $fillable AGAR BISA DI-UPDATE
    protected $fillable = [
        'nama',
        'tanggal_mulai',
        'tanggal_selesai',
        'is_active'
    ];
    
    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'is_active' => 'boolean',
    ];
    
    /**
     * Get the first currently active PKL period (backward compatibility).
     */
    public static function activePeriod()
    {
        return static::where('is_active', true)->first();
    }

    /**
     * Get all currently active PKL periods.
     */
    public static function activePeriods()
    {
        return static::where('is_active', true)->get();
    }
}