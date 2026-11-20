<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fibrosis_scores', function (Blueprint $table) {
            $table->id('fibrosis_score_id');

            $table->foreignId('visit_id')
                ->unique()
                ->constrained('visits', 'visit_id');

            $table->decimal('fib4_score', 10, 4)->nullable();
            $table->decimal('apri_score', 10, 4)->nullable();
            $table->decimal('nfs_score', 10, 4)->nullable();

            $table->string('formula_version', 100)->nullable();
            $table->dateTime('calculated_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fibrosis_scores');
    }
};