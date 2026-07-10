<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('siswa_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('nis')->unique();
            // 🔥 Perbaikan: sebutkan nama tabel secara eksplisit
            $table->foreignId('jurusan_id')->constrained('jurusan')->cascadeOnDelete();
            $table->foreignId('perusahaan_id')->nullable()->constrained('perusahaan')->nullOnDelete();
            $table->foreignId('pembimbing_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('siswa_profiles');
    }
};