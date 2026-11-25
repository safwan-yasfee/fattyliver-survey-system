<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('elastography_results', function (Blueprint $table) {
            $table->foreignId('visit_id')
                ->nullable()
                ->unique()
                ->constrained('visits', 'visit_id')
                ->restrictOnDelete();

            $table->date('assessment_date')->nullable();

            $table->decimal('lsm_kpa', 8, 2)->nullable();
            $table->decimal('cap_db_m', 8, 2)->nullable();

            $table->string('fibrosis_stage', 30)->nullable();
            $table->string('steatosis_grade', 30)->nullable();

            $table->text('notes')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('elastography_results', function (Blueprint $table) {
            $table->dropForeign(['visit_id']);
            $table->dropUnique(['visit_id']);

            $table->dropColumn([
                'visit_id',
                'assessment_date',
                'lsm_kpa',
                'cap_db_m',
                'fibrosis_stage',
                'steatosis_grade',
                'notes',
            ]);
        });
    }
};