<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('perusahaan', function (Blueprint $table) {
            $table->foreignId('periode_pkl_id')
                  ->nullable()
                  ->after('id')
                  ->constrained('periode_pkls')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('perusahaan', function (Blueprint $table) {
            $table->dropForeign(['periode_pkl_id']);
            $table->dropColumn('periode_pkl_id');
        });
    }
};
