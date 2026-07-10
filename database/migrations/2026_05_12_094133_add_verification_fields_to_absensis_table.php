<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('absensis', function (Blueprint $table) {
            $table->string('ip_address')->nullable()->after('foto');
            $table->string('eotag_token')->nullable()->after('ip_address');
            $table->boolean('is_verified')->default(false)->after('eotag_token');
        });
    }

    public function down(): void {
        Schema::table('absensis', function (Blueprint $table) {
            $table->dropColumn(['ip_address', 'eotag_token', 'is_verified']);
        });
    }
};