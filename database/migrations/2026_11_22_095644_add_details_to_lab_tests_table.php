<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lab_tests', function (Blueprint $table) {
            $table->string('test_code', 30)->nullable()->unique();
            $table->string('name')->nullable();
            $table->string('unit', 50)->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
        });
    }

    public function down(): void
    {
        Schema::table('lab_tests', function (Blueprint $table) {
            $table->dropUnique(['test_code']);

            $table->dropColumn([
                'test_code',
                'name',
                'unit',
                'description',
                'is_active',
            ]);
        });
    }
};