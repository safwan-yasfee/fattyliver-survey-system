<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('eligibility_reviews', 'patient_id')) {
            Schema::table('eligibility_reviews', function (Blueprint $table) {
                $table->unsignedBigInteger('patient_id')->nullable();

                $table->foreign('patient_id')
                    ->references('patient_id')
                    ->on('patients')
                    ->restrictOnDelete();
            });
        }

        if (! Schema::hasColumn('eligibility_reviews', 'reviewed_by')) {
            Schema::table('eligibility_reviews', function (Blueprint $table) {
                $table->foreignId('reviewed_by')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();
            });
        }

        if (! Schema::hasColumn('eligibility_reviews', 'reviewed_at')) {
            Schema::table('eligibility_reviews', function (Blueprint $table) {
                $table->timestamp('reviewed_at')->nullable();
            });
        }

        if (! Schema::hasColumn('eligibility_reviews', 'notes')) {
            Schema::table('eligibility_reviews', function (Blueprint $table) {
                $table->text('notes')->nullable();
            });
        }
    }

    public function down(): void
    {
        // This corrective migration preserves existing columns and data.
    }
};