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
    Schema::create('penilaians', function (Blueprint $table) {
        $table->id();
        $table->foreignId('pembimbing_id')->constrained('users')->cascadeOnDelete();
        $table->foreignId('siswa_user_id')->constrained('users')->cascadeOnDelete();
        $table->string('kategori'); // sikap, kinerja, kedisiplinan, dll
        $table->integer('nilai')->default(0); // 0-100
        $table->text('keterangan')->nullable();
        $table->timestamps();
    });
}
public function down(): void { Schema::dropIfExists('penilaians'); }
};
