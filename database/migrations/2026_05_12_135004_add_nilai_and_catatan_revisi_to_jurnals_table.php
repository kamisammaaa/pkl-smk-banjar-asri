<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jurnals', function (Blueprint $table) {
            // Tambah kolom nilai jika belum ada
            if (!Schema::hasColumn('jurnals', 'nilai')) {
                $table->integer('nilai')->nullable()->after('status')->comment('Nilai 0-100 untuk jurnal ini');
            }
            
            // Tambah kolom catatan_revisi jika belum ada
            if (!Schema::hasColumn('jurnals', 'catatan_revisi')) {
                $table->text('catatan_revisi')->nullable()->after('nilai')->comment('Catatan revisi dari pembimbing');
            }
        });
    }

    public function down(): void
    {
        Schema::table('jurnals', function (Blueprint $table) {
            $table->dropColumn(['nilai', 'catatan_revisi']);
        });
    }
};