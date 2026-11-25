<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\View\View;

class AccountSetupController extends Controller
{
    public function show(
        Request $request,
        string $token
    ): View|RedirectResponse {
        $email = trim((string) $request->query('email', ''));

        $user = User::query()
            ->where('email', $email)
            ->where('status', 'pending')
            ->where('is_active', false)
            ->first();

        if (
            ! $user ||
            ! Password::broker()->tokenExists($user, $token)
        ) {
            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => $this->invalidInvitationMessage(),
                ]);
        }

        return view('auth.account-setup', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'token' => [
                'required',
                'string',
                'max:255',
            ],
            'email' => [
                'required',
                'email',
                'max:255',
            ],
            'password' => [
                'required',
                'string',
                'confirmed',
                'max:72',
                PasswordRule::min(12)
                    ->letters()
                    ->mixedCase()
                    ->numbers(),
            ],
        ]);

        $status = Password::broker()->reset(
            [
                'email' => $data['email'],
                'password' => $data['password'],
                'password_confirmation' =>
                    $request->input('password_confirmation'),
                'token' => $data['token'],

                // Only pending, inactive accounts can use setup.
                'status' => 'pending',
                'is_active' => false,
            ],
            function (User $user, string $password): void {
                $user->password = Hash::make($password);
                 $user->password_set_at = now();

                $user->setRememberToken(
                    Str::random(60)
                );

                // Password setup does not approve the account
                // or change its assigned role.
                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status !== Password::PasswordReset) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => $this->invalidInvitationMessage(),
                ]);
        }

        return redirect()
            ->route('login')
            ->with(
                'success',
                'Password saved successfully. Your account must be approved by an Admin before you can sign in.'
            );
    }

    private function invalidInvitationMessage(): string
    {
        return 'This invitation is invalid, expired or unavailable for this account. Ask the Admin for help.';
    }
}