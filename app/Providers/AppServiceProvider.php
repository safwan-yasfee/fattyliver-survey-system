<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\Gate::define(
    'view-patient-records',
    function (\App\Models\User $user): bool {
        $roleName = strtolower(trim((string) $user->role?->name));

        return (bool) $user->is_active
            && in_array(
                $roleName,
                ['admin', 'doctor', 'assistant'],
                true
            );
    }
);
        
    }
}
