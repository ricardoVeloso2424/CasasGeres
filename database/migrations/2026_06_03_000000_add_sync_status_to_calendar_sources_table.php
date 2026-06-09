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
        Schema::table('calendar_sources', function (Blueprint $table): void {
            $table->string('last_sync_status')->nullable()->after('last_synced_at');
            $table->text('last_sync_error')->nullable()->after('last_sync_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('calendar_sources', function (Blueprint $table): void {
            $table->dropColumn(['last_sync_status', 'last_sync_error']);
        });
    }
};
