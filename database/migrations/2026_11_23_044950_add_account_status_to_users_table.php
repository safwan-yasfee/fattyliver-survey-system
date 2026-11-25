
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('status', 20)
                ->default('pending');

            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('approved_at')->nullable();
            $table->timestamp('last_login_at')->nullable();
        });

        // Preserve existing active users.
        DB::table('users')
            ->where('is_active', 1)
            ->update(['status' => 'active']);

        DB::table('users')
            ->where('is_active', 0)
            ->update(['status' => 'pending']);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn([
                'status',
                'approved_by',
                'approved_at',
                'last_login_at',
            ]);
        });
    }
};
