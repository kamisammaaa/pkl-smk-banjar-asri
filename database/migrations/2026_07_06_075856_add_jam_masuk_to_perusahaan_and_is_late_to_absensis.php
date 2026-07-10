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
        // Tambah kolom jam masuk & toleransi ke tabel perusahaan
        Schema::table('perusahaan', function (Blueprint $table) {
            if (!Schema::hasColumn('perusahaan', 'jam_masuk')) {
                $table->time('jam_masuk')->default('07:30:00')
                      ->comment('Jam masuk wajib perusahaan (format HH:MM:SS)');
            }
            if (!Schema::hasColumn('perusahaan', 'toleransi_menit')) {
                $table->unsignedSmallInteger('toleransi_menit')->default(15)
                      ->comment('Toleransi keterlambatan dalam menit');
            }
        });

        // Tambah kolom deteksi keterlambatan ke tabel absensis
        Schema::table('absensis', function (Blueprint $table) {
            if (!Schema::hasColumn('absensis', 'is_late')) {
                $table->boolean('is_late')->default(false)->nullable()
                      ->comment('Apakah siswa terlambat?');
            }
            if (!Schema::hasColumn('absensis', 'terlambat_menit')) {
                $table->unsignedSmallInteger('terlambat_menit')->nullable()
                      ->comment('Berapa menit keterlambatan dari jam masuk + toleransi');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('perusahaan', function (Blueprint $table) {
            foreach (['jam_masuk', 'toleransi_menit'] as $col) {
                if (Schema::hasColumn('perusahaan', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        Schema::table('absensis', function (Blueprint $table) {
            foreach (['is_late', 'terlambat_menit'] as $col) {
                if (Schema::hasColumn('absensis', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
