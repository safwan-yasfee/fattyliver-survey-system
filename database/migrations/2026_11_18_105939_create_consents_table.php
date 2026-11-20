<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consents', function (Blueprint $table) {
            $table->id('consent_id');

            $table->foreignId('patient_id')
                ->constrained('patients', 'patient_id');

            $table->foreignId('consent_version_id')
                ->constrained('consent_versions', 'consent_version_id');

            $table->foreignId('recorded_by')
                ->nullable()
                ->constrained('users', 'id');

            $table->dateTime('signed_at')->nullable();
            $table->dateTime('withdrawn_at')->nullable();

            $table->string('patient_signature_path')->nullable();
            $table->string('witness_name')->nullable();
            $table->string('witness_signature_path')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consents');
    }
};