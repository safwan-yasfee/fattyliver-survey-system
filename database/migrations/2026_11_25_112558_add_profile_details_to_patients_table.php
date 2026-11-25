<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->string('occupation', 150)->nullable();
            $table->string('education_level', 100)->nullable();
            $table->text('address')->nullable();
            $table->string('email', 255)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn([
                'occupation',
                'education_level',
                'address',
                'email',
            ]);
        });
    }
};