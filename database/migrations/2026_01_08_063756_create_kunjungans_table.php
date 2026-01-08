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
        Schema::create('kunjungan', function (Blueprint $table) {
            $table->id('id_kunjungan');

            $table->foreignId('id_tamu')
                ->constrained('tamu', 'id_tamu')
                ->cascadeOnDelete();

            $table->string('tujuan_kunjungan');
            $table->date('tanggal_kunjungan');
            $table->time('jam_mulai');
            $table->time('jam_selesai')->nullable();

            $table->enum('status', [
                'pending',
                'approved',
                'canceled',
                'done',
            ])->default('pending');

            $table->text('alasan_batal')->nullable();
            $table->string('token_approval')->unique()->nullable();
            $table->timestamp('expired_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kunjungan');
    }
};
