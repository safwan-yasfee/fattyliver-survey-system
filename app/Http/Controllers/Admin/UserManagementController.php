<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function index(): View
    {
        Gate::authorize('manage-users');

        $users = User::with('role')
            ->orderByDesc('id')
            ->paginate(10);

        $roles = Role::whereIn('name', [
            'Doctor',
            'Assistant',
        ])
            ->orderBy('name')
            ->get();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('manage-users');

        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],
            'role_id' => [
                'required',
                'integer',
                Rule::exists('roles', 'id')
                    ->whereIn('name', ['Doctor', 'Assistant']),
            ],
        ]);

        $user = new User();
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->role_id = $data['role_id'];

        // The recipient replaces this unknown random password
        // through the invitation link.
        $user->password = Hash::make(Str::random(64));
        $user->password_set_at = null;
        $user->status = 'pending';
        $user->is_active = false;
        $user->save();

        $sent = $this->sendInvitation($user);

        return redirect()
            ->route('admin.users.index')
            ->with(
                $sent ? 'success' : 'warning',
                $sent
                    ? 'Account created. Invitation submitted to the configured mail service.'
                    : 'Account created, but the invitation failed. Use Resend Invitation.'
            );
    }

    public function approve(
        Request $request,
        User $user
    ): RedirectResponse {
        Gate::authorize('manage-users');

        $data = $request->validate([
            'role_id' => [
                'required',
                'integer',
                Rule::exists('roles', 'id')
                    ->whereIn('name', ['Doctor', 'Assistant']),
            ],
        ]);

        DB::transaction(function () use (
            $request,
            $user,
            $data
        ): void {
            $account = User::query()
                ->lockForUpdate()
                ->findOrFail($user->getKey());

            $this->checkStaffAccount($account);

            if (
                $account->status !== 'pending' ||
                (bool) $account->is_active
            ) {
                throw ValidationException::withMessages([
                    'account' => 'Only pending, inactive accounts can be approved.',
                ]);
            }

            if (! $account->password_set_at) {
                throw ValidationException::withMessages([
                    'account' => 'The user must set their password using the invitation link before approval.',
                ]);
            }

            $account->role_id = $data['role_id'];
            $account->status = 'active';
            $account->is_active = true;
            $account->approved_by = $request->user()->id;
            $account->approved_at = now();
            $account->save();
        });

        return back()->with(
            'success',
            'Account approved successfully. The user can now sign in.'
        );
    }

    public function suspend(User $user): RedirectResponse
    {
        Gate::authorize('manage-users');

        $this->checkStaffAccount($user);

        if ($user->status !== 'active') {
            throw ValidationException::withMessages([
                'account' => 'Only active accounts can be suspended.',
            ]);
        }

        $user->status = 'suspended';
        $user->is_active = false;

        // Invalidate existing remember-me credentials.
        $user->setRememberToken(Str::random(60));

        $user->save();

        return back()->with(
            'success',
            'Account suspended successfully.'
        );
    }

    public function reactivate(User $user): RedirectResponse
    {
        Gate::authorize('manage-users');

        $this->checkStaffAccount($user);

        if ($user->status !== 'suspended') {
            throw ValidationException::withMessages([
                'account' => 'Only suspended accounts can be reactivated.',
            ]);
        }

        $user->status = 'active';
        $user->is_active = true;
        $user->save();

        return back()->with(
            'success',
            'Account reactivated successfully.'
        );
    }

    public function resendInvite(User $user): RedirectResponse
    {
        Gate::authorize('manage-users');

        $this->checkStaffAccount($user);

        if (
            $user->status !== 'pending' ||
            (bool) $user->is_active
        ) {
            throw ValidationException::withMessages([
                'account' => 'Invitations can only be sent to pending, inactive accounts.',
            ]);
        }

        $sent = $this->sendInvitation($user);

        return back()->with(
            $sent ? 'success' : 'warning',
            $sent
                ? 'Invitation submitted to the configured mail service. Use the latest link.'
                : 'Invitation failed. Check storage/logs/laravel.log.'
        );
    }

    private function checkStaffAccount(User $user): void
    {
        abort_unless(
            in_array(
                $user->role?->name,
                ['Doctor', 'Assistant'],
                true
            ),
            403,
            'Only Doctor and Assistant accounts can be changed here.'
        );
    }

    private function sendInvitation(User $user): bool
    {
        try {
            $token = Password::broker()->createToken($user);

            $url = route('account.setup.show', [
                'token' => $token,
                'email' => $user->email,
            ]);

            Mail::send(
                'emails.account-invitation',
                compact('user', 'url'),
                function ($message) use ($user): void {
                    $message
                        ->to($user->email)
                        ->subject(
                            'Set up your Fatty Liver Management account'
                        );
                }
            );

            return true;
        } catch (\Throwable $exception) {
            report($exception);

            return false;
        }
    }
}