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
        Schema::table('kunjungan', function (Blueprint $table) {
            // Composite index for ordering (tanggal_kunjungan DESC, jam_mulai DESC)
            $table->index(['tanggal_kunjungan', 'jam_mulai'], 'idx_kunjungan_tanggal_jam');

            // Index for status filtering
            $table->index('status', 'idx_kunjungan_status');

            // Index for tujuan_kunjungan search
            $table->index('tujuan_kunjungan', 'idx_kunjungan_tujuan');
        });

        // Index for tamu table (used in whereHas)
        Schema::table('tamu', function (Blueprint $table) {
            $table->index(['nama_tamu', 'email_tamu', 'instansi_tamu'], 'idx_tamu_search');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kunjungan', function (Blueprint $table) {
            $table->dropIndex('idx_kunjungan_tanggal_jam');
            $table->dropIndex('idx_kunjungan_status');
            $table->dropIndex('idx_kunjungan_tujuan');
        });

        Schema::table('tamu', function (Blueprint $table) {
            $table->dropIndex('idx_tamu_search');
        });
    }
};