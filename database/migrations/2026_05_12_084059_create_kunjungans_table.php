<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up(): void
{
    Schema::create('kunjungans', function (Blueprint $table) {
        $table->id();
        $table->foreignId('pembimbing_id')->constrained('users')->cascadeOnDelete();
        $table->foreignId('siswa_user_id')->constrained('users')->cascadeOnDelete();
        $table->foreignId('perusahaan_id')->constrained('perusahaan')->cascadeOnDelete();
        $table->date('tanggal');
        $table->text('catatan')->nullable();
        $table->string('foto')->nullable();
        $table->timestamps();
    });
}
public function down(): void { Schema::dropIfExists('kunjungans'); }
};
