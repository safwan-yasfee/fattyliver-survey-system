<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $addStatus = ! Schema::hasColumn('users', 'status');

        if ($addStatus) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('status', 20)->default('pending');
            });
        }

        if (! Schema::hasColumn('users', 'approved_by')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreignId('approved_by')
                    ->nullable()
                    ->constrained('users', 'id')
                    ->nullOnDelete();
            });
        }

        if (! Schema::hasColumn('users', 'approved_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('approved_at')->nullable();
            });
        }

        if (! Schema::hasColumn('users', 'last_login_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('last_login_at')->nullable();
            });
        }

        // Initialize statuses only when this migration creates the column.
        if ($addStatus) {
            DB::table('users')
                ->where('is_active', 1)
                ->update(['status' => 'active']);
        }
    }

    public function down(): void
    {
        // Preserve approval columns that may predate this migration.
    }
};