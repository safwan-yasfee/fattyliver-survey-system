<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $credentials['is_active'] = true;

        if (! Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => 'Invalid credentials or inactive account.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    public function dashboard()
    {
        $user = Auth::user()->load('role');

        if (! $user->isActive() || ! $user->role ||
            ! in_array($user->role->name, ['Admin', 'Doctor', 'Assistant'], true)) {
            Auth::logout();

            return redirect()->route('login')
                ->withErrors(['email' => 'Account access is not authorized.']);
        }

        return view('dashboard', compact('user'));
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
