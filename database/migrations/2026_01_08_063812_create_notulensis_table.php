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
        Schema::create('notulensi', function (Blueprint $table) {
            $table->id('id_notulensi');

            $table->foreignId('id_kunjungan')
                ->constrained('kunjungan', 'id_kunjungan')
                ->cascadeOnDelete();

            $table->text('anggota_rapat');
            $table->longText('isi_notulensi');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notulensi');
    }
};
