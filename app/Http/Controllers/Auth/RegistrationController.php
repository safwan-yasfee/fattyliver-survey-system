<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RegistrationController extends Controller
{
    public function showRegistration(): View
    {
        $roles = DB::table('roles')
            ->whereIn('name', [
                'Admin',
                'Doctor',
                'Assistant',
            ])
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('auth.register', compact('roles'));
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],
            'role_id' => [
                'required',
                'integer',
                Rule::exists('roles', 'id')->whereIn('name', [
                    'Admin',
                    'Doctor',
                    'Assistant',
                ]),
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'max:72',
                'confirmed',
            ],
        ], [], [
            'role_id' => 'role',
        ]);

        $user = new User();
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = Hash::make($data['password']);
        $user->role_id = $data['role_id'];
        $user->is_active = false;
        $user->save();

        return redirect()
            ->route('login')
            ->with(
                'success',
                'Account registered. An administrator must approve your account before you can sign in.'
            );
    }
}