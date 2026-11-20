<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clinical_alerts', function (Blueprint $table) {
            $table->id('clinical_alert_id');

            $table->foreignId('visit_id')
                ->constrained('visits', 'visit_id');

            $table->string('alert_type', 50);
            $table->text('message');

            $table->enum('status', [
                'open',
                'resolved',
            ])->default('open');

            $table->dateTime('resolved_at')->nullable();

            $table->timestamps();

            $table->index(['visit_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clinical_alerts');
    }
};