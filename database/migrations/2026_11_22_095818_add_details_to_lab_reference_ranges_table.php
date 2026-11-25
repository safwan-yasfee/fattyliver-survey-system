<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lab_reference_ranges', function (Blueprint $table) {
            $table->foreignId('lab_test_id')
                ->nullable()
                ->constrained('lab_tests', 'id')
                ->restrictOnDelete();

            $table->string('sex', 20)->nullable();

            $table->unsignedSmallInteger('min_age')->nullable();
            $table->unsignedSmallInteger('max_age')->nullable();

            $table->decimal('lower_limit', 14, 4)->nullable();
            $table->decimal('upper_limit', 14, 4)->nullable();

            $table->string('unit', 50)->nullable();
            $table->string('laboratory_name')->nullable();
            $table->text('notes')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('lab_reference_ranges', function (Blueprint $table) {
            $table->dropForeign(['lab_test_id']);

            $table->dropColumn([
                'lab_test_id',
                'sex',
                'min_age',
                'max_age',
                'lower_limit',
                'upper_limit',
                'unit',
                'laboratory_name',
                'notes',
            ]);
        });
    }
};