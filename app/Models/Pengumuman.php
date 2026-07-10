<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Pengumuman extends Model
{
    protected $table = 'pengumumans';
    protected $fillable = ['judul', 'isi', 'target', 'admin_id', 'published_at', 'is_active'];
    protected $casts = ['published_at' => 'datetime', 'is_active' => 'boolean'];
    
    public function admin() { return $this->belongsTo(User::class, 'admin_id'); }
    
    public function scopeForUser($query, $user)
    {
        return $query->where('is_active', true)
            ->where(function($q) use ($user) {
                $q->where('target', 'semua')
                  ->orWhere('target', $user->role)
                  ->orWhereNull('target');
            })
            ->where('published_at', '<=', now())
            ->latest();
    }
}