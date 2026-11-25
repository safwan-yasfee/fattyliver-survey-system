@extends('layouts.auth')

@section('title', 'Reset Password')

@section('content')
    <h1>Set a new password</h1>

    <p class="intro">
        Choose a new password for your account.
    </p>

    <form method="POST" action="{{ route('password.update') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">

        <div class="mb-3">
            <label for="email" class="form-label">
                Email Address
            </label>

            <input id="email"
                   name="email"
                   type="email"
                   class="form-control"
                   value="{{ old('email', $email) }}"
                   maxlength="255"
                   autocomplete="username"
                   required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">
                New Password
            </label>

            <input id="password"
                   name="password"
                   type="password"
                   class="form-control"
                   placeholder="At least 8 characters"
                   minlength="8"
                   maxlength="72"
                   autocomplete="new-password"
                   required>
        </div>

        <div class="mb-4">
            <label for="password_confirmation" class="form-label">
                Confirm New Password
            </label>

            <input id="password_confirmation"
                   name="password_confirmation"
                   type="password"
                   class="form-control"
                   minlength="8"
                   maxlength="72"
                   autocomplete="new-password"
                   required>
        </div>

        <button type="submit" class="btn btn-auth">
            Reset Password
        </button>
    </form>

    <p class="small-copy text-center mt-4 mb-0">
        <a href="{{ route('password.request') }}"
           class="auth-link">
            Request a new reset link
        </a>
    </p>
@endsection