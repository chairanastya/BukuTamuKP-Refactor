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
        Schema::create('kunjungan_karyawan', function (Blueprint $table) {
            $table->id('id_kunjungan_karyawan');

            $table->foreignId('id_kunjungan')
                ->constrained('kunjungan', 'id_kunjungan')
                ->cascadeOnDelete();

            $table->foreignId('id_karyawan')
                ->constrained('karyawan', 'id_karyawan')
                ->cascadeOnDelete();

            $table->unique(['id_kunjungan', 'id_karyawan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kunjungan_karyawan');
    }
};
