<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('elastography_results', function (Blueprint $table) {
            $table->id('elastography_result_id');

            $table->foreignId('visit_id')
                ->unique()
                ->constrained('visits', 'visit_id');

            $table->date('assessment_date')->nullable();

            $table->decimal('lsm_kpa', 8, 2)->nullable();
            $table->decimal('cap_db_m', 8, 2)->nullable();

            $table->string('fibrosis_stage', 30)->nullable();
            $table->string('steatosis_grade', 30)->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('elastography_results');
    }
};