<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Create Account | Fatty Liver Research</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>
        body {
            background: #edf4f3;
            color: #16324f;
        }

        .registration-card {
            max-width: 580px;
            margin: 40px auto;
            border: 0;
            border-radius: 20px;
        }

        .form-control,
        .form-select {
            padding: 12px;
            border-radius: 9px;
        }

        .btn-primary {
            background: #087c6c;
            border-color: #087c6c;
        }

        .btn-primary:hover {
            background: #066456;
            border-color: #066456;
        }

        a {
            color: #087c6c;
        }
    </style>
</head>

<body>
    <main class="container py-3">
        <section class="card registration-card shadow-sm">
            <div class="card-body p-4 p-md-5">
                <p class="fw-semibold text-secondary">
                    Fatty Liver Research Management System
                </p>

                <h1 class="h3 fw-bold">Create Account</h1>

                <p class="text-secondary mb-4">
                    Select your role and enter your account details.
                    An Admin will approve your access.
                </p>

                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label">
                            Full Name
                        </label>

                        <input
                            id="name"
                            name="name"
                            type="text"
                            class="form-control"
                            value="{{ old('name') }}"
                            maxlength="255"
                            autocomplete="name"
                            required
                        >
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">
                            Email Address
                        </label>

                        <input
                            id="email"
                            name="email"
                            type="email"
                            class="form-control"
                            value="{{ old('email') }}"
                            maxlength="255"
                            autocomplete="email"
                            required
                        >
                    </div>

                    <div class="mb-3">
                        <label for="role_id" class="form-label">
                            Role
                        </label>

                        <select
                            id="role_id"
                            name="role_id"
                            class="form-select"
                            required
                        >
                            <option value="">Select your role</option>

                            @foreach ($roles as $role)
                                <option
                                    value="{{ $role->id }}"
                                    @selected(old('role_id') == $role->id)
                                >
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">
                            Password
                        </label>

                        <input
                            id="password"
                            name="password"
                            type="password"
                            class="form-control"
                            minlength="8"
                            maxlength="72"
                            autocomplete="new-password"
                            required
                        >

                        <div class="form-text">
                            Use at least 8 characters.
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label">
                            Confirm Password
                        </label>

                        <input
                            id="password_confirmation"
                            name="password_confirmation"
                            type="password"
                            class="form-control"
                            minlength="8"
                            maxlength="72"
                            autocomplete="new-password"
                            required
                        >
                    </div>

                    <button type="submit"
                            class="btn btn-primary w-100 py-3">
                        Create Account
                    </button>
                </form>

                <p class="text-center mt-4 mb-0">
                    Already have an account?
                    <a href="{{ route('login') }}">Sign In</a>
                </p>
            </div>
        </section>
    </main>
</body>
</html>