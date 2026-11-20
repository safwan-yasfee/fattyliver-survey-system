<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('eligibility_reviews', function (Blueprint $table) {
            $table->id('eligibility_review_id');

            $table->foreignId('patient_id')
                ->constrained('patients', 'patient_id');

            $table->foreignId('reviewed_by')
                ->nullable()
                ->constrained('users', 'id');

            $table->enum('status', [
                'eligible',
                'pending',
                'not_eligible',
            ])->default('pending');

            $table->text('reason')->nullable();
            $table->dateTime('reviewed_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eligibility_reviews');
    }
};