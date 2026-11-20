<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lab_reference_ranges', function (Blueprint $table) {
            $table->id('lab_reference_range_id');

            $table->foreignId('lab_test_id')
                ->constrained('lab_tests', 'lab_test_id');

            $table->string('sex', 20)->nullable();

            $table->unsignedSmallInteger('minimum_age')->nullable();
            $table->unsignedSmallInteger('maximum_age')->nullable();

            $table->decimal('lower_limit', 14, 4)->nullable();
            $table->decimal('upper_limit', 14, 4)->nullable();

            $table->string('unit', 50)->nullable();
            $table->string('laboratory_name')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_reference_ranges');
    }
};