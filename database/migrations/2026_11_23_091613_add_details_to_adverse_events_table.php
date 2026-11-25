<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('adverse_events', function (Blueprint $table) {
            $table->foreignId('visit_id')
                ->nullable()
                ->constrained('visits', 'visit_id')
                ->restrictOnDelete();

            $table->date('event_date')->nullable();
            $table->text('description')->nullable();

            $table->string('severity', 30)->nullable();
            $table->boolean('is_serious')->nullable();

            $table->text('treatment')->nullable();
            $table->text('outcome')->nullable();
            $table->text('notes')->nullable();

            $table->foreignId('recorded_by')
                ->nullable()
                ->constrained('users', 'id')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('adverse_events', function (Blueprint $table) {
            $table->dropForeign(['visit_id']);
            $table->dropForeign(['recorded_by']);

            $table->dropColumn([
                'visit_id',
                'event_date',
                'description',
                'severity',
                'is_serious',
                'treatment',
                'outcome',
                'notes',
                'recorded_by',
            ]);
        });
    }
};