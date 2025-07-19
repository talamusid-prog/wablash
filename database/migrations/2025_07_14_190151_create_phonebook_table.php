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
        Schema::create('phonebook', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone_number');
            $table->string('email')->nullable();
            $table->text('notes')->nullable();
            $table->string('group')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Index untuk pencarian yang lebih cepat
            $table->index(['name', 'phone_number']);
            $table->index('group');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phonebook');
    }
};
