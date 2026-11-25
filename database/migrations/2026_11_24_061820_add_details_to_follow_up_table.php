<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('follow_up', function (Blueprint $table) {
            $table->foreignId('visit_id')
                ->nullable()
                ->constrained('visits', 'visit_id')
                ->restrictOnDelete();

            $table->date('follow_up_date')->nullable();

            $table->text('progress_notes')->nullable();
            $table->text('assessment')->nullable();
            $table->text('plan')->nullable();

            $table->date('next_follow_up_date')->nullable();

            $table->foreignId('recorded_by')
                ->nullable()
                ->constrained('users', 'id')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('follow_up', function (Blueprint $table) {
            $table->dropForeign(['visit_id']);
            $table->dropForeign(['recorded_by']);

            $table->dropColumn([
                'visit_id',
                'follow_up_date',
                'progress_notes',
                'assessment',
                'plan',
                'next_follow_up_date',
                'recorded_by',
            ]);
        });
    }
};