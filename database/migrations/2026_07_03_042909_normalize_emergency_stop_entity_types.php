<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Normalize existing emergency stop entity types to lowercase.
     */
    public function up(): void
    {
        DB::table('ai_emergency_stops')
            ->where('entity_type', '!=', DB::raw('LOWER(entity_type)'))
            ->update(['entity_type' => DB::raw('LOWER(entity_type)')]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Normalization is irreversible — we cannot know what the original case was.
    }
};
