<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lifestyle_profile', function (Blueprint $table) {
            $table->id('lifestyle_profile_id');

            $table->foreignId('patient_id')
                ->unique()
                ->constrained('patients', 'patient_id');

            $table->text('physical_activity')->nullable();
            $table->text('dietary_habits')->nullable();
            $table->text('smoking_history')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lifestyle_profile');
    }
};