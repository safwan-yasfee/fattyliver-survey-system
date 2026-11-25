<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lab_results', function (Blueprint $table) {
            $table->foreignId('visit_id')
                ->nullable()
                ->constrained('visits', 'visit_id')
                ->restrictOnDelete();

            $table->foreignId('lab_test_id')
                ->nullable()
                ->constrained('lab_tests', 'id')
                ->restrictOnDelete();

            $table->decimal('result_value', 14, 4)->nullable();
            $table->text('result_text')->nullable();
            $table->string('unit', 50)->nullable();
            $table->date('tested_at')->nullable();
            $table->text('notes')->nullable();

            $table->unique(['visit_id', 'lab_test_id']);
        });
    }

    public function down(): void
    {
        Schema::table('lab_results', function (Blueprint $table) {
            $table->dropUnique(['visit_id', 'lab_test_id']);

            $table->dropForeign(['visit_id']);
            $table->dropForeign(['lab_test_id']);

            $table->dropColumn([
                'visit_id',
                'lab_test_id',
                'result_value',
                'result_text',
                'unit',
                'tested_at',
                'notes',
            ]);
        });
    }
};