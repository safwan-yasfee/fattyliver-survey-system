<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('medical_history', function (Blueprint $table) {
            $table->foreignId('patient_id')
                ->nullable()
                ->unique()
                ->constrained('patients', 'patient_id')
                ->cascadeOnDelete();

            $table->text('diseases')->nullable();
            $table->text('alcohol_history')->nullable();
            $table->text('drug_history')->nullable();
            $table->text('biopsy_history')->nullable();
            $table->text('notes')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('medical_history', function (Blueprint $table) {
            $table->dropForeign(['patient_id']);
            $table->dropUnique(['patient_id']);

            $table->dropColumn([
                'patient_id',
                'diseases',
                'alcohol_history',
                'drug_history',
                'biopsy_history',
                'notes',
            ]);
        });
    }
};