<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('adherence', function (Blueprint $table) {
            $table->foreignId('treatment_id')
                ->nullable()
                ->unique()
                ->constrained('treatments', 'id')
                ->restrictOnDelete();

            $table->date('assessment_date')->nullable();

            $table->unsignedInteger('tablets_expected')->nullable();
            $table->unsignedInteger('tablets_taken')->nullable();

            $table->decimal('adherence_percentage', 5, 2)->nullable();

            $table->text('notes')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('adherence', function (Blueprint $table) {
            $table->dropForeign(['treatment_id']);
            $table->dropUnique(['treatment_id']);

            $table->dropColumn([
                'treatment_id',
                'assessment_date',
                'tablets_expected',
                'tablets_taken',
                'adherence_percentage',
                'notes',
            ]);
        });
    }
};