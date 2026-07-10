<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            
            if (!Schema::hasColumn('absensis', 'status')) {
                $table->string('status', 20)->default('hadir')->after('tanggal');
            }
            if (!Schema::hasColumn('absensis', 'alasan')) {
                $table->text('alasan')->nullable()->after('status');
            }
            if (!Schema::hasColumn('absensis', 'bukti_file')) {
                $table->string('bukti_file')->nullable()->after('alasan');
            }
            if (!Schema::hasColumn('absensis', 'is_verified')) {
                $table->boolean('is_verified')->default(false)->after('bukti_file');
            }
            if (!Schema::hasColumn('absensis', 'verified_by')) {
                $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete()->after('is_verified');
            }
            if (!Schema::hasColumn('absensis', 'verified_at')) {
                $table->timestamp('verified_at')->nullable()->after('verified_by');
            }
            if (!Schema::hasColumn('absensis', 'latitude')) {
                $table->decimal('latitude', 10, 8)->nullable()->after('check_out');
            }
            if (!Schema::hasColumn('absensis', 'longitude')) {
                $table->decimal('longitude', 10, 8)->nullable()->after('latitude');
            }
            if (!Schema::hasColumn('absensis', 'lokasi_nama')) {
                $table->string('lokasi_nama')->nullable()->after('longitude');
            }
            
            // ️ Catatan: Mengubah kolom existing (check_in -> nullable) 
            // membutuhkan package doctrine/dbal. Jika tidak urgent, bisa diskip dulu.
            // Jika ingin lanjut, jalankan: composer require doctrine/dbal
        });
    }

    public function down(): void
    {
        Schema::table('absensis', function (Blueprint $table) {
            $cols = ['status', 'alasan', 'bukti_file', 'is_verified', 'verified_by', 'verified_at', 'latitude', 'longitude', 'lokasi_nama'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('absensis', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};