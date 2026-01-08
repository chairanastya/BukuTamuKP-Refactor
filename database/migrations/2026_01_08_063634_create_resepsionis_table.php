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
        Schema::create('resepsionis', function (Blueprint $table) {
            $table->id('id_resepsionis');
            $table->foreignId('id_karyawan')
                ->constrained('karyawan', 'id_karyawan')
                ->cascadeOnDelete();

            $table->string('nama_resepsionis');
            $table->string('email_resepsionis')->unique();
            $table->string('password_resepsionis');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resepsioni');
    }
};
