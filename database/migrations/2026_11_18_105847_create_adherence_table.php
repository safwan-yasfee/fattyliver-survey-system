<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('adherence', function (Blueprint $table) {
            $table->id('adherence_id');

            $table->foreignId('visit_id')
                ->unique()
                ->constrained('visits', 'visit_id');

            $table->unsignedInteger('tablets_given')->nullable();
            $table->unsignedInteger('tablets_returned')->nullable();
            $table->unsignedInteger('tablets_expected')->nullable();

            $table->decimal('adherence_percentage', 5, 2)->nullable();

            $table->date('assessment_date')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('adherence');
    }
};