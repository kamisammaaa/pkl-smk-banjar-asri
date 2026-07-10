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
    Schema::create('perusahaan', function (Blueprint $table) {
        $table->id();
        $table->string('nama');
        $table->text('alamat');
        $table->string('kontak')->nullable();
        $table->foreignId('pembimbing_id')->nullable()->constrained('users')->nullOnDelete();
        $table->timestamps();
    });
}
public function down(): void { Schema::dropIfExists('perusahaan'); }
};
