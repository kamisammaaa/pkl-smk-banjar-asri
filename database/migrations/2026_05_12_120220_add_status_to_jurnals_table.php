<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jurnals', function (Blueprint $table) {
            $table->enum('status', ['menunggu', 'disetujui', 'revisi'])
                  ->default('menunggu')
                  ->after('foto');
        });
    }

    public function down(): void
    {
        Schema::table('jurnals', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};