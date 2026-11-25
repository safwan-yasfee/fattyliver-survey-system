@extends('layouts.auth')

@section('title', 'Forgot Password')

@section('content')
    <h1>Forgot your password?</h1>

    <p class="intro">
        Enter your registered email address to request
        a password reset link.
    </p>

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="mb-4">
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
                   autocomplete="email"
                   required>
        </div>

        <button type="submit" class="btn btn-auth">
            Send Reset Link
        </button>
    </form>

    <p class="small-copy text-center mt-4 mb-0">
        Remember your password?
        <a href="{{ route('login') }}"
           class="auth-link"
           data-auth-slide>
            Back to Sign In
        </a>
    </p>
@endsection