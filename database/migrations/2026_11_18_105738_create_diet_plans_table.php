<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('diet_plans', function (Blueprint $table) {
            $table->id('diet_plan_id');

            $table->foreignId('patient_id')
                ->constrained('patients', 'patient_id');

            $table->foreignId('doctor_user_id')
                ->nullable()
                ->constrained('users', 'id');

            $table->date('plan_date');
            $table->unsignedInteger('daily_calorie_target')->nullable();

            $table->text('diet_chart_bn')->nullable();
            $table->text('diet_chart_en')->nullable();
            $table->text('education_notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diet_plans');
    }
};