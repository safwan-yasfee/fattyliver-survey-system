<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::define('view-patient-records', function (User $user): bool {
            $role = strtolower(trim((string) $user->role?->name));

            return (bool) $user->is_active
                && in_array($role, ['admin', 'doctor', 'assistant'], true);
        });

        \Illuminate\Support\Facades\Gate::define(
            'review-patient-eligibility',
            function (\App\Models\User $user): bool {
                $role = strtolower(trim((string) $user->role?->name));

                return (bool) $user->is_active
                    && $user->status === 'active'
                    && in_array($role, ['admin', 'doctor'], true);
            }
        );


        \Illuminate\Support\Facades\Gate::define(
            'record-patient-consent',
            function (\App\Models\User $user): bool {
                $role = strtolower(trim((string) $user->role?->name));

                return (bool) $user->is_active
                    && $user->status === 'active'
                    && in_array($role, ['admin', 'doctor'], true);
            }
        );

        Gate::define('manage-patient-records', function (User $user): bool {
            $role = strtolower(trim((string) $user->role?->name));

            return (bool) $user->is_active
                && $role === 'admin';
        });
        \Illuminate\Support\Facades\Gate::define(
            'manage-users',
            function (\App\Models\User $user): bool {
                $role = strtolower(trim((string) $user->role?->name));

                return (bool) $user->is_active
                    && $user->status === 'active'
                    && $role === 'admin';
            }
        );


        Gate::define('archive-patients', function (User $user): bool {
            $role = strtolower(trim((string) $user->role?->name));

            return (bool) $user->is_active
                && in_array($role, ['admin', 'assistant'], true);
        });


    }


}