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
        Schema::table('resepsionis', function (Blueprint $table) {
            $table->string('token_setup')->nullable()->after('password_resepsionis');
            $table->timestamp('token_setup_expired_at')->nullable()->after('token_setup');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resepsionis', function (Blueprint $table) {
            $table->dropColumn(['token_setup', 'token_setup_expired_at']);
        });
    }
};
