<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visits', function (Blueprint $table) {
            $table->id('visit_id');

            $table->foreignId('patient_id')
                ->constrained('patients', 'patient_id');

            $table->string('time_point', 20);
            $table->date('due_date')->nullable();
            $table->dateTime('visited_at')->nullable();

            $table->timestamps();

            $table->unique(['patient_id', 'time_point']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};