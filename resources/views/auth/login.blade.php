@extends('layouts.auth')

@section('title', 'Sign In')

@section('content')
    <h1>Welcome back</h1>

    <p class="intro">
        Sign in to access your clinical and research workspace.
    </p>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">
                Email Address
            </label>

            <input id="email"
                   name="email"
                   type="email"
                   class="form-control"
                   value="{{ old('email') }}"
                   placeholder="you@example.com"
                   maxlength="255"
                   autocomplete="username"
                   required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">
                Password
            </label>

            <input id="password"
                   name="password"
                   type="password"
                   class="form-control"
                   placeholder="Enter your password"
                   autocomplete="current-password"
                   required>
        </div>

        <div class="text-end mb-4">
            <a href="{{ route('password.request') }}"
               class="auth-link small-copy"
               data-auth-slide>
                Forgot password?
            </a>
        </div>

        <button type="submit" class="btn btn-auth mt-2">
            Sign In
        </button>
    </form>

    <p class="small-copy text-center mt-4 mb-0">
        Need an account? Contact the system administrator.
    </p>
@endsection