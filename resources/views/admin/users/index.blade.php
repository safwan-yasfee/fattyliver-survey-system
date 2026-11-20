<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>User Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            padding: 30px;
        }
        .container {
            max-width: 1100px;
            margin: auto;
            background: white;
            padding: 28px;
            border-radius: 12px;
        }
        h1 { color: #175b70; }
        .btn {
            display: inline-block;
            background: #176b83;
            color: white;
            padding: 10px 16px;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            cursor: pointer;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 22px 0;
        }
        th, td {
            padding: 13px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }
        th { background: #eaf4f7; }
        .success { color: green; }
        .inactive { color: red; }
        .table-wrap { overflow-x: auto; }
    </style>
</head>
<body>
<div class="container">
    <h1>User Management</h1>

    <p>
        <a href="{{ route('dashboard') }}">Dashboard</a>
    </p>

    <a class="btn" href="{{ route('admin.users.create') }}">
        + Add New User
    </a>

    @if (session('success'))
        <p class="success">{{ session('success') }}</p>
    @endif

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->role?->name ?? 'None' }}</td>
                        <td>
                            {{ $user->is_active ? 'Active' : 'Inactive' }}
                        </td>
                        <td>
                            @if ($user->id !== auth()->id() &&
                                 $user->role?->name !== 'Admin')
                                <form method="POST"
                                      action="{{ route('admin.users.status', $user) }}">
                                    @csrf
                                    @method('PATCH')

                                    <button class="btn" type="submit">
                                        {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>
                            @else
                                Protected
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">No users found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $users->links() }}
</div>
</body>
</html>
