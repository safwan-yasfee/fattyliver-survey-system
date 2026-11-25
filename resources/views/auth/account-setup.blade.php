@extends('layouts.auth')

@section('title', 'Set Up Account')

@section('content')
    <h1>Set up your account</h1>

    <p class="intro">
        Choose your password. Admin approval is required before signing in.
    </p>

    <form method="POST" action="{{ route('account.setup.store') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">

        <div class="mb-3">
            <label for="email" class="form-label">Email Address</label>

            <input id="email"
                   name="email"
                   type="email"
                   class="form-control"
                   value="{{ old('email', $email) }}"
                   autocomplete="username"
                   required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">New Password</label>

            <input id="password"
                   name="password"
                   type="password"
                   class="form-control"
                   minlength="12"
                   maxlength="72"
                   autocomplete="new-password"
                   aria-describedby="password-help"
                   required>

            <div id="password-help" class="form-text">
                Use at least 12 characters, including uppercase,
                lowercase letters and a number.
            </div>
        </div>

        <div class="mb-4">
            <label for="password_confirmation" class="form-label">
                Confirm Password
            </label>

            <input id="password_confirmation"
                   name="password_confirmation"
                   type="password"
                   class="form-control"
                   minlength="12"
                   maxlength="72"
                   autocomplete="new-password"
                   required>
        </div>

        <button type="submit" class="btn btn-auth">
            Save Password
        </button>
    </form>
@endsection