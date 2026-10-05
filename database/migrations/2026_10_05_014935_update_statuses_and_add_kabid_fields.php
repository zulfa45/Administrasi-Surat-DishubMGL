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
        // For PostgreSQL, we need to drop the constraint to change the ENUM values safely.
        // It's safer to drop the constraint directly.
        $this->dropCheckConstraint('incoming_letters', 'status');
        $this->dropCheckConstraint('assignments', 'status');

        Schema::table('incoming_letters', function (Blueprint $table) {
            $table->string('status')->default('baru')->change();
        });

        Schema::table('assignments', function (Blueprint $table) {
            $table->string('status')->default('belum_dibaca')->change();
            $table->string('file_tindak_lanjut')->nullable();
            $table->text('catatan_revisi')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assignments', function (Blueprint $table) {
            $table->dropColumn(['file_tindak_lanjut', 'catatan_revisi']);
            // We won't revert the status column back to ENUM to prevent data loss.
        });
    }
    
    private function dropCheckConstraint($table, $column)
    {
        $constraints = DB::select("
            SELECT conname
            FROM pg_constraint c
            JOIN pg_class t ON c.conrelid = t.oid
            JOIN pg_attribute a ON a.attrelid = t.oid AND a.attnum = ANY(c.conkey)
            WHERE t.relname = ? AND a.attname = ? AND c.contype = 'c'
        ", [$table, $column]);

        foreach ($constraints as $constraint) {
            DB::statement("ALTER TABLE {$table} DROP CONSTRAINT {$constraint->conname}");
        }
    }
};
