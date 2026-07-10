<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('perusahaan_data', function (Blueprint $table) {
            $table->renameColumn('alamat_perusahaan', 'alamat_pembimbing');
        });
    }

    public function down(): void
    {
        Schema::table('perusahaan_data', function (Blueprint $table) {
            $table->renameColumn('alamat_pembimbing', 'alamat_perusahaan');
        });
    }
};
