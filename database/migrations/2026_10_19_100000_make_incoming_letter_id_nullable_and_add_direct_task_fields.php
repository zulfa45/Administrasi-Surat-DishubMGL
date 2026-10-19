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
        // Izinkan incoming_letter_id bernilai NULL untuk tugas langsung (tanpa surat masuk)
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE assignments ALTER COLUMN incoming_letter_id DROP NOT NULL;');
        } elseif (DB::getDriverName() !== 'sqlite') {
            Schema::table('assignments', function (Blueprint $table) {
                $table->foreignId('incoming_letter_id')->nullable()->change();
            });
        }

        Schema::table('assignments', function (Blueprint $table) {
            $table->string('judul_tugas')->nullable();
            $table->string('sifat')->default('Biasa')->nullable();
            $table->string('file_tugas')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assignments', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn(['judul_tugas', 'sifat', 'file_tugas', 'created_by']);
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE assignments ALTER COLUMN incoming_letter_id SET NOT NULL;');
        }
    }
};
