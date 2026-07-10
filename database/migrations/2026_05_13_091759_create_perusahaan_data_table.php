<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('perusahaan_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('nama_perusahaan');
            $table->text('alamat_pembimbing');
            $table->string('nama_pembimbing');
            $table->string('ttl_pembimbing'); // Format: Jakarta, 12 Januari 1980
            $table->string('no_telp');
            $table->boolean('is_verified')->default(false); // Admin bisa verifikasi nanti
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perusahaan_data');
    }
};
