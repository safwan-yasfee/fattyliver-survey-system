@extends('layouts.auth')

@section('title', 'Create Account')

@section('content')
    <h1>Create your account</h1>

    <p class="intro">
        Select your role and enter your details.
        An Admin must approve your account before you can sign in.
    </p>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">
                Full Name
            </label>

            <input id="name"
                   name="name"
                   type="text"
                   class="form-control"
                   value="{{ old('name') }}"
                   placeholder="Enter your full name"
                   maxlength="255"
                   autocomplete="name"
                   required>
        </div>

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
            <label for="role_id" class="form-label">
                Requested Role
            </label>

            <select id="role_id"
                    name="role_id"
                    class="form-select"
                    required>
                <option value="">Select your role</option>

                @foreach ($roles as $role)
                    <option value="{{ $role->id }}"
                            @selected((string) old('role_id') === (string) $role->id)>
                        {{ $role->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-sm-6">
                <label for="password" class="form-label">
                    Password
                </label>

                <input id="password"
                       name="password"
                       type="password"
                       class="form-control"
                       placeholder="Create password"
                       minlength="8"
                       maxlength="72"
                       autocomplete="new-password"
                       aria-describedby="password-help"
                       required>
            </div>

            <div class="col-sm-6">
                <label for="password_confirmation" class="form-label">
                    Confirm Password
                </label>

                <input id="password_confirmation"
                       name="password_confirmation"
                       type="password"
                       class="form-control"
                       placeholder="Repeat password"
                       minlength="8"
                       maxlength="72"
                       autocomplete="new-password"
                       required>
            </div>
        </div>

        <p id="password-help" class="small-copy">
            Use at least 8 characters.
        </p>

        <button type="submit" class="btn btn-auth">
            Create Account
        </button>
    </form>

    <p class="small-copy text-center mt-4 mb-0">
        Already registered?
        <a href="{{ route('login') }}"
           class="auth-link"
           data-auth-slide>
            Sign in
        </a>
    </p>
@endsection