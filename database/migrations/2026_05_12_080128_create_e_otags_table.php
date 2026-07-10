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
    Schema::create('e_otags', function (Blueprint $table) {
        $table->id();
        $table->foreignId('siswa_user_id')->constrained('users')->cascadeOnDelete();
        $table->string('token')->unique();
        $table->timestamp('check_in_at');
        $table->string('ip_address');
        $table->enum('status', ['valid', 'used', 'expired'])->default('valid');
        $table->timestamps();
    });
}
public function down(): void { Schema::dropIfExists('e_otags'); }
};
