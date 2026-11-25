<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lifestyle_profile', function (Blueprint $table) {
            $table->foreignId('patient_id')
                ->nullable()
                ->unique()
                ->constrained('patients', 'patient_id')
                ->cascadeOnDelete();

            $table->text('physical_activity')->nullable();
            $table->text('dietary_habits')->nullable();
            $table->text('smoking_history')->nullable();
            $table->text('notes')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('lifestyle_profile', function (Blueprint $table) {
            $table->dropForeign(['patient_id']);
            $table->dropUnique(['patient_id']);

            $table->dropColumn([
                'patient_id',
                'physical_activity',
                'dietary_habits',
                'smoking_history',
                'notes',
            ]);
        });
    }
};