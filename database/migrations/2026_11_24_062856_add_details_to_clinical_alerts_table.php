<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clinical_alerts', function (Blueprint $table) {
            $table->string('alert_type', 100)->nullable();
            $table->text('message')->nullable();

            $table->foreignId('recorded_by')
                ->nullable()
                ->constrained('users', 'id')
                ->nullOnDelete();

            $table->timestamp('resolved_at')->nullable();

            $table->foreignId('resolved_by')
                ->nullable()
                ->constrained('users', 'id')
                ->nullOnDelete();

            $table->text('resolution_notes')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('clinical_alerts', function (Blueprint $table) {
            $table->dropForeign(['recorded_by']);
            $table->dropForeign(['resolved_by']);

            $table->dropColumn([
                'alert_type',
                'message',
                'recorded_by',
                'resolved_at',
                'resolved_by',
                'resolution_notes',
            ]);
        });
    }
};