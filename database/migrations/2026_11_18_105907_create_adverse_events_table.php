<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('adverse_events', function (Blueprint $table) {
            $table->id('adverse_event_id');

            $table->foreignId('visit_id')
                ->constrained('visits', 'visit_id');

            $table->date('event_date')->nullable();
            $table->text('description');

            $table->string('severity', 50)->nullable();
            $table->boolean('is_serious')->nullable();

            $table->text('treatment_given')->nullable();
            $table->text('outcome')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('adverse_events');
    }
};