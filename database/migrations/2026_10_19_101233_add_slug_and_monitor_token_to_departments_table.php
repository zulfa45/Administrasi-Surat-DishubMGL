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
        Schema::table('departments', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('name');
            $table->string('monitor_token', 64)->nullable()->after('status');
        });

        // Backfill existing departments
        $departments = \Illuminate\Support\Facades\DB::table('departments')->get();
        foreach ($departments as $dept) {
            $baseSlug = \Illuminate\Support\Str::slug($dept->name);
            $slug = $baseSlug;
            $counter = 1;
            while (\Illuminate\Support\Facades\DB::table('departments')->where('slug', $slug)->where('id', '!=', $dept->id)->exists()) {
                $slug = $baseSlug . '-' . $counter++;
            }

            \Illuminate\Support\Facades\DB::table('departments')
                ->where('id', $dept->id)
                ->update([
                    'slug' => $slug,
                    'monitor_token' => \Illuminate\Support\Str::random(32),
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->dropColumn(['slug', 'monitor_token']);
        });
    }
};
