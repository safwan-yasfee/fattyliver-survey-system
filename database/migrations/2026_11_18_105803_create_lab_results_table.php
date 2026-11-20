<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lab_results', function (Blueprint $table) {
            $table->id('lab_result_id');

            $table->foreignId('visit_id')
                ->constrained('visits', 'visit_id');

            $table->foreignId('lab_test_id')
                ->constrained('lab_tests', 'lab_test_id');

            $table->decimal('result_value', 14, 4)->nullable();
            $table->string('result_text')->nullable();
            $table->string('unit', 50)->nullable();

            $table->dateTime('tested_at')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->unique(['visit_id', 'lab_test_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_results');
    }
};