<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    private function checkAdmin(Request $request): void
    {
        $user = $request->user();

        abort_unless(
            $user &&
            $user->isActive() &&
            $user->hasRole('Admin'),
            403,
            'Access denied. Admin only.'
        );
    }

    public function index(Request $request)
    {
        $this->checkAdmin($request);

        $users = User::with('role')
            ->orderByDesc('id')
            ->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    public function create(Request $request)
    {
        $this->checkAdmin($request);

        $roles = Role::whereIn('name', [
            'Doctor',
            'Assistant'
        ])->get();

        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $this->checkAdmin($request);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email'
            ],
            'password' => [
                'required',
                'confirmed',
                Password::min(12)
            ],
            'role_id' => [
                'required',
                Rule::exists('roles', 'id')->where(
                    fn ($query) => $query->whereIn(
                        'name',
                        ['Doctor', 'Assistant']
                    )
                )
            ],
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role_id' => $data['role_id'],
            'is_active' => true,
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    public function toggleStatus(Request $request, User $user)
    {
        $this->checkAdmin($request);

        abort_if(
            $user->id === $request->user()->id ||
            $user->hasRole('Admin'),
            403,
            'Admin accounts cannot be changed here.'
        );

        $user->is_active = ! $user->is_active;
        $user->save();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User status updated.');
    }
}
