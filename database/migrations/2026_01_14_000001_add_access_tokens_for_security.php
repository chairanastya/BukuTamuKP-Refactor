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
        // Tambahkan kolom access_token untuk keamanan akses gambar
        Schema::table('tamu', function (Blueprint $table) {
            $table->string('ktp_access_token', 64)->unique()->nullable()->after('ktp_url');
            $table->index('ktp_access_token');
        });

        Schema::table('dokumentasi', function (Blueprint $table) {
            $table->string('access_token', 64)->unique()->nullable()->after('dokumentasi_url');
            $table->index('access_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tamu', function (Blueprint $table) {
            $table->dropIndex(['ktp_access_token']);
            $table->dropColumn('ktp_access_token');
        });

        Schema::table('dokumentasi', function (Blueprint $table) {
            $table->dropIndex(['access_token']);
            $table->dropColumn('access_token');
        });
    }
};
