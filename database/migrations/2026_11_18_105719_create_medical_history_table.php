<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medical_history', function (Blueprint $table) {
            $table->id('medical_history_id');

            $table->foreignId('patient_id')
                ->unique()
                ->constrained('patients', 'patient_id');

            $table->text('diseases')->nullable();
            $table->text('alcohol_history')->nullable();
            $table->text('drug_history')->nullable();
            $table->text('biopsy_history')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medical_history');
    }
};