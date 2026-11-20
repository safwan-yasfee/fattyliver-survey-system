
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | Fatty Liver System</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            background: #f0f5f8;
            font-family: Arial, sans-serif;
        }
        .login-box {
            width: 100%;
            max-width: 420px;
            background: white;
            padding: 35px;
            border-radius: 12px;
            box-shadow: 0 8px 30px #00000012;
        }
        h2 { color: #175b70; margin-bottom: 8px; }
        p { color: #64748b; margin-bottom: 25px; }
        label { display: block; margin: 15px 0 7px; }
        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #cbd5e1;
            border-radius: 7px;
        }
        button {
            width: 100%;
            background: #176b83;
            color: white;
            border: 0;
            padding: 13px;
            margin-top: 22px;
            border-radius: 7px;
            cursor: pointer;
        }
        .error { color: #b91c1c; }
    </style>
</head>
<body>
<div class="login-box">
    <h2>Fatty Liver System</h2>
    <p>Secure Patient Management Portal</p>

    @if ($errors->any())
        <div class="error">
            {{ $errors->first() }}
        </div>
    @endif


    @if (session('success'))
    <div class="alert alert-success" role="status">
        {{ session('success') }}
    </div>
@endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <label>Email</label>
        <input type="email" name="email"
               value="{{ old('email') }}" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <button type="submit">Login</button>
    </form>
    <p class="text-center mt-3">
    Need an account?
    <a href="{{ route('register') }}">Create Account</a>
</p>
</div>
</body>
</html>
