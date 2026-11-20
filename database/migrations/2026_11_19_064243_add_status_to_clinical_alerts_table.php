<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clinical_alerts', function (Blueprint $table) {
            $table->enum('status', [
                'open',
                'resolved',
            ])->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('clinical_alerts', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};