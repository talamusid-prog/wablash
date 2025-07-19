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
        Schema::create('blast_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('campaign_id');
            $table->string('phone_number');
            $table->text('message_content');
            $table->enum('status', ['pending', 'sent', 'failed'])->default('pending');
            $table->timestamp('sent_at')->nullable();
            $table->text('error_message')->nullable();
            $table->string('whatsapp_message_id')->nullable();
            $table->string('attachment_path')->nullable();
            $table->timestamps();

            $table->foreign('campaign_id')->references('id')->on('blast_campaigns')->onDelete('cascade');
            $table->foreign('whatsapp_message_id')->references('message_id')->on('whatsapp_messages')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blast_messages');
    }
};
