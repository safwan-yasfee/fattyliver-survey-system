<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveAccount
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        $user = Auth::guard('web')->user();

        if (! $user) {
            return $next($request);
        }

        if (
            (int) $user->is_active !== 1 ||
            $user->status !== 'active'
        ) {
            Auth::guard('web')->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Account not active',
                ], 403);
            }

            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => 'Account not active',
                ]);
        }

        return $next($request);
    }
}