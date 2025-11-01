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
        Schema::table('blast_messages', function (Blueprint $table) {
            if (!Schema::hasColumn('blast_messages', 'attachment_path')) {
                $table->string('attachment_path')->nullable()->after('whatsapp_message_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blast_messages', function (Blueprint $table) {
            if (Schema::hasColumn('blast_messages', 'attachment_path')) {
                $table->dropColumn('attachment_path');
            }
        });
    }
};
