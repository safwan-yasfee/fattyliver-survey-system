<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Define consent document versions first.
        Schema::table('consent_versions', function (Blueprint $table) {
            $table->string('version_code', 50)
                ->nullable()
                ->unique();

            $table->string('title')->nullable();

            $table->date('effective_date')->nullable();

            $table->boolean('is_active')->default(false);

            $table->text('notes')->nullable();
        });

        // Record each patient's consent against a document version.
        Schema::table('consents', function (Blueprint $table) {
            $table->unsignedBigInteger('patient_id')->nullable();

            $table->foreign('patient_id')
                ->references('patient_id')
                ->on('patients')
                ->restrictOnDelete();

            $table->foreignId('consent_version_id')
                ->nullable()
                ->constrained('consent_versions')
                ->restrictOnDelete();

            $table->timestamp('signed_at')->nullable();

            $table->foreignId('recorded_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('withdrawn_at')->nullable();

            $table->foreignId('withdrawn_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->text('withdrawal_reason')->nullable();

            $table->text('notes')->nullable();
        });
    }

    public function down(): void
    {
        // Remove dependent foreign keys before their columns.
        Schema::table('consents', function (Blueprint $table) {
            $table->dropForeign(['patient_id']);
            $table->dropForeign(['consent_version_id']);
            $table->dropForeign(['recorded_by']);
            $table->dropForeign(['withdrawn_by']);

            $table->dropColumn([
                'patient_id',
                'consent_version_id',
                'signed_at',
                'recorded_by',
                'withdrawn_at',
                'withdrawn_by',
                'withdrawal_reason',
                'notes',
            ]);
        });

        Schema::table('consent_versions', function (Blueprint $table) {
            $table->dropUnique(['version_code']);

            $table->dropColumn([
                'version_code',
                'title',
                'effective_date',
                'is_active',
                'notes',
            ]);
        });
    }
};