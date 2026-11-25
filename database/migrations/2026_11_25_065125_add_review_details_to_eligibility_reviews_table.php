<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('eligibility_reviews', function (Blueprint $table) {
            $table->unsignedBigInteger('patient_id')->nullable();

            $table->foreign('patient_id')
                ->references('patient_id')
                ->on('patients')
                ->restrictOnDelete();

            $table->foreignId('reviewed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('reviewed_at')->nullable();

            $table->text('notes')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('eligibility_reviews', function (Blueprint $table) {
            $table->dropForeign(['patient_id']);
            $table->dropForeign(['reviewed_by']);

            $table->dropColumn([
                'patient_id',
                'reviewed_by',
                'reviewed_at',
                'notes',
            ]);
        });
    }
};