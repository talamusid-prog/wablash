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
        Schema::create('blast_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('message_template');
            $table->json('target_numbers');
            $table->enum('status', ['draft', 'scheduled', 'running', 'completed', 'failed'])->default('draft');
            $table->timestamp('scheduled_at')->nullable();
            $table->integer('sent_count')->default(0);
            $table->integer('failed_count')->default(0);
            $table->integer('total_count')->default(0);
            $table->string('session_id');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('session_id')->references('session_id')->on('whatsapp_sessions')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blast_campaigns');
    }
};
