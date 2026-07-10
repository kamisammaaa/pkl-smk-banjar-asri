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
    Schema::table('absensis', function (Blueprint $table) {
        // Tambah kolom status untuk rekap: hadir, sakit, izin, alpha
        $table->enum('status', ['hadir', 'sakit', 'izin', 'alpha'])->default('hadir')->after('is_verified');
        $table->text('keterangan_status')->nullable()->after('status');
    });
}
public function down(): void
{
    Schema::table('absensis', function (Blueprint $table) {
        $table->dropColumn(['status', 'keterangan_status']);
    });
}
};
