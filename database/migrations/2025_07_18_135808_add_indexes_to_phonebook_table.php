<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('phonebook', function (Blueprint $table) {
            // Index untuk group (sering digunakan dalam WHERE clause)
            if (!$this->indexExists('phonebook', 'phonebook_group_index')) {
                $table->index(['group'], 'phonebook_group_index');
            }
            
            // Index untuk phone_number (untuk filter dan pencarian)
            if (!$this->indexExists('phonebook', 'phonebook_phone_index')) {
                $table->index(['phone_number'], 'phonebook_phone_index');
            }
            
            // Index untuk is_active (untuk filter kontak aktif)
            if (!$this->indexExists('phonebook', 'phonebook_active_index')) {
                $table->index(['is_active'], 'phonebook_active_index');
            }
            
            // Index untuk name (untuk pencarian dan sorting)
            if (!$this->indexExists('phonebook', 'phonebook_name_index')) {
                $table->index(['name'], 'phonebook_name_index');
            }
            
            // Composite index untuk query yang sering digunakan
            if (!$this->indexExists('phonebook', 'phonebook_group_phone_index')) {
                $table->index(['group', 'phone_number'], 'phonebook_group_phone_index');
            }
            
            // Index untuk kombinasi group dan is_active
            if (!$this->indexExists('phonebook', 'phonebook_group_active_index')) {
                $table->index(['group', 'is_active'], 'phonebook_group_active_index');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('phonebook', function (Blueprint $table) {
            $table->dropIndexIfExists('phonebook_group_index');
            $table->dropIndexIfExists('phonebook_phone_index');
            $table->dropIndexIfExists('phonebook_active_index');
            $table->dropIndexIfExists('phonebook_name_index');
            $table->dropIndexIfExists('phonebook_group_phone_index');
            $table->dropIndexIfExists('phonebook_group_active_index');
        });
    }

    /**
     * Check if index exists
     */
    private function indexExists($table, $indexName): bool
    {
        $indexes = DB::select("SHOW INDEX FROM {$table}");
        foreach ($indexes as $index) {
            if ($index->Key_name === $indexName) {
                return true;
            }
        }
        return false;
    }
};
