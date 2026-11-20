<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clinical_alerts', function (Blueprint $table) {
            $table->foreignId('visit_id')
                ->nullable()
                ->constrained('visits', 'visit_id');
        });
    }

    public function down(): void
    {
        Schema::table('clinical_alerts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('visit_id');
        });
    }
};