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
        Schema::table('contacts', function (Blueprint $table) {
            // Index untuk type dan group_id (sering digunakan dalam WHERE clause)
            if (!$this->indexExists('contacts', 'contacts_type_group_id_index')) {
                $table->index(['type', 'group_id'], 'contacts_type_group_id_index');
            }
            
            // Index untuk type dan phone_number (untuk filter kontak valid)
            if (!$this->indexExists('contacts', 'contacts_type_phone_index')) {
                $table->index(['type', 'phone_number'], 'contacts_type_phone_index');
            }
            
            // Index untuk status (untuk filter kontak aktif)
            if (!$this->indexExists('contacts', 'contacts_status_index')) {
                $table->index(['status'], 'contacts_status_index');
            }
            
            // Index untuk name (untuk pencarian dan sorting)
            if (!$this->indexExists('contacts', 'contacts_name_index')) {
                $table->index(['name'], 'contacts_name_index');
            }
            
            // Composite index untuk query yang sering digunakan
            if (!$this->indexExists('contacts', 'contacts_type_status_phone_index')) {
                $table->index(['type', 'status', 'phone_number'], 'contacts_type_status_phone_index');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropIndexIfExists('contacts_type_group_id_index');
            $table->dropIndexIfExists('contacts_type_phone_index');
            $table->dropIndexIfExists('contacts_status_index');
            $table->dropIndexIfExists('contacts_name_index');
            $table->dropIndexIfExists('contacts_type_status_phone_index');
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
