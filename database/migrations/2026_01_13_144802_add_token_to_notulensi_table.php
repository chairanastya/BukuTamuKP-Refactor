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
        Schema::table('notulensi', function (Blueprint $table) {
            $table->string('token_karyawan')->unique()->nullable()->after('isi_notulensi');
            $table->string('token_tamu')->unique()->nullable()->after('token_karyawan');
            $table->timestamp('expired_at')->nullable()->after('token_tamu');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notulensi', function (Blueprint $table) {
            $table->dropColumn(['token_karyawan', 'token_tamu', 'expired_at']);
        });
    }
};
