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
        Schema::table('ai_pending_decisions', function (Blueprint $table) {
            $table->string('custom_decision')->nullable()->after('status');
            $table->text('override_reason')->nullable()->after('custom_decision');
            $table->timestamp('overridden_at')->nullable()->after('override_reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_pending_decisions', function (Blueprint $table) {
            $table->dropColumn(['custom_decision', 'override_reason', 'overridden_at']);
        });
    }
};
