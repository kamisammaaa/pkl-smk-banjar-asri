<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penilaian_akhir', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembimbing_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('siswa_user_id')->constrained('users')->cascadeOnDelete();
            
            // Komponen penilaian
            $table->integer('nilai_absensi')->default(0)->comment('Nilai absensi 0-100');
            $table->integer('nilai_jurnal')->default(0)->comment('Rata-rata nilai jurnal 0-100');
            $table->integer('nilai_sikap')->default(0)->comment('Nilai sikap & kinerja 0-100');
            
            // Nilai akhir (30% absen + 40% jurnal + 30% sikap)
            $table->integer('nilai_akhir')->default(0)->comment('Nilai akhir PKL 0-100');
            
            $table->text('catatan_akhir')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
            
            // Unique: satu siswa hanya boleh dinilai sekali per pembimbing
            $table->unique(['pembimbing_id', 'siswa_user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penilaian_akhir');
    }
};