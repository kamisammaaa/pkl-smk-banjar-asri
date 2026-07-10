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
    Schema::create('pengumumans', function (Blueprint $table) {
        $table->id();
        $table->string('judul');
        $table->text('isi');
        $table->enum('target', ['semua', 'siswa', 'pembimbing'])->default('semua');
        $table->foreignId('admin_id')->constrained('users')->cascadeOnDelete();
        $table->timestamp('published_at')->nullable();
        $table->boolean('is_active')->default(true);
        $table->timestamps();
    });
}
public function down(): void { Schema::dropIfExists('pengumumans'); }
};
