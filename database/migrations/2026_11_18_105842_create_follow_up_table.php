<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('follow_up', function (Blueprint $table) {
            $table->id('follow_up_id');

            $table->foreignId('visit_id')
                ->constrained('visits', 'visit_id');

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users', 'id');

            $table->date('follow_up_date');
            $table->text('description');
            $table->text('treatment')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('follow_up');
    }
};