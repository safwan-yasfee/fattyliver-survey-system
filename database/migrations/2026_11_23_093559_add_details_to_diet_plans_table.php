<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('diet_plans', function (Blueprint $table) {
            $table->foreignId('visit_id')
                ->nullable()
                ->unique()
                ->constrained('visits', 'visit_id')
                ->restrictOnDelete();

            $table->date('counselling_date')->nullable();

            $table->text('diet_advice')->nullable();
            $table->text('exercise_advice')->nullable();
            $table->text('goals')->nullable();
            $table->text('notes')->nullable();

            $table->foreignId('recorded_by')
                ->nullable()
                ->constrained('users', 'id')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('diet_plans', function (Blueprint $table) {
            $table->dropForeign(['visit_id']);
            $table->dropForeign(['recorded_by']);
            $table->dropUnique(['visit_id']);

            $table->dropColumn([
                'visit_id',
                'counselling_date',
                'diet_advice',
                'exercise_advice',
                'goals',
                'notes',
                'recorded_by',
            ]);
        });
    }
};