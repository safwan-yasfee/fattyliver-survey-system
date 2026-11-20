<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visit_vitals', function (Blueprint $table) {
            $table->id('visit_vital_id');

            $table->foreignId('visit_id')
                ->unique()
                ->constrained('visits', 'visit_id')
                ->cascadeOnDelete();

            $table->decimal('weight_kg', 5, 2)->nullable();
            $table->decimal('height_cm', 5, 2)->nullable();
            $table->decimal('waist_cm', 5, 2)->nullable();

            $table->unsignedSmallInteger('systolic_bp')->nullable();
            $table->unsignedSmallInteger('diastolic_bp')->nullable();
            $table->unsignedSmallInteger('heart_rate')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visit_vitals');
    }
};