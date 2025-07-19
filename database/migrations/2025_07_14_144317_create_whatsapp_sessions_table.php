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
        Schema::create('whatsapp_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('session_id')->unique();
            $table->string('phone_number')->nullable();
            $table->enum('status', ['connecting', 'qr_ready', 'connected', 'disconnected', 'error'])->default('connecting');
            $table->text('qr_code')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_activity')->nullable();
            $table->json('device_info')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_sessions');
    }
};
