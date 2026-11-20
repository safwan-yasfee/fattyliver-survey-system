<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consent_versions', function (Blueprint $table) {
            $table->id('consent_version_id');

            $table->string('version_code', 30)->unique();
            $table->string('title');

            $table->longText('consent_text_bn');
            $table->longText('consent_text_en')->nullable();

            $table->date('effective_date')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consent_versions');
    }
};