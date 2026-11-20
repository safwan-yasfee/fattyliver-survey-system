<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add User | Fatty Liver System</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            margin: 0;
            padding: 40px 16px;
        }
        .container {
            max-width: 520px;
            margin: auto;
            background: white;
            padding: 32px;
            border-radius: 12px;
            box-shadow: 0 6px 25px #00000010;
        }
        h2 { color: #175b70; }
        label {
            display: block;
            margin: 17px 0 7px;
            font-weight: bold;
        }
        input, select {
            width: 100%;
            padding: 12px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
        }
        button {
            width: 100%;
            background: #176b83;
            color: white;
            border: 0;
            padding: 13px;
            border-radius: 6px;
            margin-top: 24px;
            cursor: pointer;
        }
        .error { color: #b91c1c; }
        a { color: #176b83; }
    </style>
</head>
<body>
<div class="container">

    <h2>Add New User</h2>
    <p>Create a Doctor or Assistant account.</p>

    <a href="{{ route('admin.users.index') }}">
        Back to User Management
    </a>

    @if ($errors->any())
        <ul class="error">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form method="POST"
          action="{{ route('admin.users.store') }}">
        @csrf

        <label>Full Name</label>
        <input type="text" name="name"
               value="{{ old('name') }}" required>

        <label>Email Address</label>
        <input type="email" name="email"
               value="{{ old('email') }}" required>

        <label>Role</label>
        <select name="role_id" required>
            <option value="">Select Role</option>
            @foreach ($roles as $role)
                <option value="{{ $role->id }}"
                    @selected(old('role_id') == $role->id)>
                    {{ $role->name }}
                </option>
            @endforeach
        </select>

        <label>Password</label>
        <input type="password" name="password"
               minlength="12" required>

        <label>Confirm Password</label>
        <input type="password"
               name="password_confirmation"
               minlength="12" required>

        <button type="submit">Create User</button>
    </form>

</div>
</body>
</html>
