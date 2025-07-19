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
        Schema::create('whatsapp_messages', function (Blueprint $table) {
            $table->id();
            $table->string('session_id');
            $table->string('message_id')->unique();
            $table->string('from_number');
            $table->string('to_number');
            $table->string('phone_number')->nullable();
            $table->text('message')->nullable();
            $table->enum('message_type', ['text', 'image', 'video', 'audio', 'document', 'location'])->default('text');
            $table->text('content')->nullable();
            $table->string('media_url')->nullable();
            $table->enum('status', ['pending', 'sent', 'delivered', 'read', 'failed'])->default('pending');
            $table->enum('direction', ['in', 'out'])->default('out');
            $table->timestamp('timestamp');
            $table->timestamp('sent_at')->nullable();
            $table->text('error_message')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->foreign('session_id')->references('session_id')->on('whatsapp_sessions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_messages');
    }
};
