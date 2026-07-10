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
    Schema::create('absensis', function (Blueprint $table) {
        $table->id();
        $table->foreignId('siswa_user_id')->constrained('users')->cascadeOnDelete();
        $table->date('tanggal');
        $table->time('check_in');
        $table->time('check_out')->nullable();
        $table->string('foto')->nullable();
        $table->timestamps();
    });
}
public function down(): void { Schema::dropIfExists('absensis'); }
};
