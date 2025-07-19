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
        Schema::table('whatsapp_sessions', function (Blueprint $table) {
            // Drop the existing enum and recreate it with qr_ready
            $table->enum('status', ['connecting', 'qr_ready', 'connected', 'disconnected', 'error'])->default('connecting')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('whatsapp_sessions', function (Blueprint $table) {
            // Revert back to original enum without qr_ready
            $table->enum('status', ['connecting', 'connected', 'disconnected', 'error'])->default('connecting')->change();
        });
    }
};
