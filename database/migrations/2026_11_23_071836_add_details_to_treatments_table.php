<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('treatments', function (Blueprint $table) {
            $table->foreignId('visit_id')
                ->nullable()
                ->constrained('visits', 'visit_id')
                ->restrictOnDelete();

            $table->string('medication_name')->nullable();
            $table->string('dose', 100)->nullable();
            $table->string('frequency', 100)->nullable();
            $table->string('route', 50)->nullable();

            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            $table->string('status', 30)->nullable();
            $table->text('notes')->nullable();

            $table->foreignId('recorded_by')
                ->nullable()
                ->constrained('users', 'id')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('treatments', function (Blueprint $table) {
            $table->dropForeign(['visit_id']);
            $table->dropForeign(['recorded_by']);

            $table->dropColumn([
                'visit_id',
                'medication_name',
                'dose',
                'frequency',
                'route',
                'start_date',
                'end_date',
                'status',
                'notes',
                'recorded_by',
            ]);
        });
    }
};