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
    Schema::create('periode_pkls', function (Blueprint $table) {
        $table->id();
        $table->string('nama'); // Contoh: "PKL Gelombang 1 2024/2025"
        $table->date('tanggal_mulai');
        $table->date('tanggal_selesai');
        $table->boolean('is_active')->default(false);
        $table->timestamps();
    });
}
public function down(): void { Schema::dropIfExists('periode_pkls'); }
};
