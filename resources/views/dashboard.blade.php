
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard | Fatty Liver System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f5f8;
            padding: 40px;
        }
        .panel {
            max-width: 650px;
            margin: auto;
            background: white;
            border-radius: 12px;
            padding: 30px;
        }
        h1 { color: #175b70; }
        button {
            background: #b91c1c;
            color: white;
            border: 0;
            padding: 12px 25px;
            border-radius: 6px;
            cursor: pointer;
        }
    </style>
</head>
<body>
<div class="panel">
    <h1>Fatty Liver Patient Management System</h1>

    <h2>Welcome, {{ $user->name }}</h2>

    <p>Email: {{ $user->email }}</p>
    <p>Role: {{ $user->role?->name }}</p>
    <p>Status: {{ $user->is_active ? 'Active' : 'Inactive' }}</p>


@if ($user->hasRole('Admin'))
    <p>
        <a href="{{ route('admin.users.index') }}">
            Manage Users
        </a>
    </p>
@endif

@can('view-patient-records')
    <div class="d-flex flex-wrap gap-2 my-3">
        <a href="{{ route('patients.index') }}" class="btn btn-primary">
            Patient List
        </a>

        <a href="{{ route('patients.create') }}"
           class="btn btn-outline-secondary">
            Add Patient
        </a>
    </div>
@endcan


@if($user->role && $user->role->name === 'Admin')
    <a href="{{ route('admin.users.index') }}"
       class="btn btn-success">
        User Management
    </a>
@endif



    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">Logout</button>
    </form>
</div>
</body>
</html>
