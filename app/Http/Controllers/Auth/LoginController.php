<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => [
                'required',
                'email',
                'max:255',
            ],
            'password' => [
                'required',
                'string',
                'max:72',
            ],
        ]);

        $email = Str::lower(trim($credentials['email']));

        $throttleKey = 'login:' . hash(
            'sha256',
            $email . '|' . $request->ip()
        );

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            event(new Lockout($request));

            $seconds = RateLimiter::availableIn($throttleKey);

            throw ValidationException::withMessages([
                'email' => "Too many login attempts. Try again in {$seconds} seconds.",
            ]);
        }

        // Count the attempt before checking credentials.
        RateLimiter::hit($throttleKey, 60);

        $credentials['is_active'] = true;
        $credentials['status'] = 'active';

        if (! Auth::guard('web')->attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => 'Invalid credentials or account not approved.',
            ]);
        }

        $user = Auth::guard('web')->user();

        $role = Str::lower(
            trim((string) $user->role?->name)
        );

        if (! in_array(
            $role,
            ['admin', 'doctor', 'assistant'],
            true
        )) {
            Auth::guard('web')->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'email' => 'Invalid credentials or account not approved.',
            ]);
        }

        // Successful login clears previous failed attempts.
        RateLimiter::clear($throttleKey);

        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}