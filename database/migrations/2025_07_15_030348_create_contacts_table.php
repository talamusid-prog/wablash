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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('session_id'); // UUID dari WhatsApp session
            $table->string('contact_id'); // ID kontak dari WhatsApp
            $table->string('name')->nullable(); // Nama kontak
            $table->string('phone_number')->nullable(); // Nomor telepon (untuk kontak individual)
            $table->enum('type', ['individual', 'group'])->default('individual'); // Tipe kontak
            $table->string('group_id')->nullable(); // ID grup (untuk kontak individual yang ada di grup)
            $table->string('group_name')->nullable(); // Nama grup (untuk kontak grup)
            $table->text('group_description')->nullable(); // Deskripsi grup
            $table->integer('group_participants_count')->nullable(); // Jumlah peserta grup
            $table->boolean('is_admin')->default(false); // Apakah admin grup
            $table->string('profile_picture')->nullable(); // URL foto profil
            $table->enum('status', ['active', 'inactive', 'blocked'])->default('active'); // Status kontak
            $table->timestamp('grabbed_at')->nullable(); // Waktu kontak di-grab
            $table->timestamps();

            // Indexes
            $table->index('session_id');
            $table->index('contact_id');
            $table->index('type');
            $table->index('group_id');
            $table->index('phone_number');
            $table->index('status');
            
            // Unique constraint untuk mencegah duplikasi kontak dalam satu session
            // Menambahkan type ke constraint agar kontak individual dan grup bisa memiliki contact_id yang sama
            $table->unique(['session_id', 'contact_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
