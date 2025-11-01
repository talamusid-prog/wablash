<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tambahkan status 'authenticated' dan 'auth_failed' ke enum kolom status
        DB::statement("ALTER TABLE `whatsapp_sessions` 
            MODIFY `status` ENUM('connecting','qr_ready','authenticated','auth_failed','connected','disconnected','error') 
            NOT NULL DEFAULT 'connecting'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan enum ke set sebelumnya (tanpa authenticated dan auth_failed)
        DB::statement("ALTER TABLE `whatsapp_sessions` 
            MODIFY `status` ENUM('connecting','qr_ready','connected','disconnected','error') 
            NOT NULL DEFAULT 'connecting'");
    }
};
