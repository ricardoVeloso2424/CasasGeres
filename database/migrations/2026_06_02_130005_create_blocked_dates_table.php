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
        Schema::create('blocked_dates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rental_unit_id')->constrained()->cascadeOnDelete();
            $table->foreignId('calendar_source_id')->nullable()->constrained()->nullOnDelete();
            $table->string('source');
            $table->string('external_uid')->nullable();
            $table->date('starts_at');
            $table->date('ends_at');
            $table->string('summary')->nullable();
            $table->timestamps();

            $table->index(['rental_unit_id', 'starts_at', 'ends_at']);
            $table->unique(['calendar_source_id', 'external_uid'], 'blocked_dates_calendar_uid_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blocked_dates');
    }
};
