<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class UserController extends Controller
{
    private function adminOnly(Request $request): void
    {
        $admin = $request->user();

        $role = $admin
            ? DB::table('roles')
                ->where('id', $admin->role_id)
                ->value('name')
            : null;

        abort_unless(
            $admin &&
            (int) $admin->is_active === 1 &&
            $admin->status === 'active' &&
            $role === 'Admin',
            403,
            'Admin access required.'
        );
    }

    private function permittedUser(User $user): void
    {
        $role = DB::table('roles')
            ->where('id', $user->role_id)
            ->value('name');

        abort_unless(
            in_array($role, ['Doctor', 'Assistant'], true),
            403,
            'This account cannot be changed here.'
        );
    }

    public function index(Request $request): View
    {
        Gate::authorize('manage-users');

        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'status' => [
                'nullable',
                Rule::in([
                    'all',
                    'awaiting_password',
                    'pending_approval',
                    'active',
                    'suspended',
                ]),
            ],
        ]);

        $search = trim($filters['search'] ?? '');
        $status = $filters['status'] ?? 'all';

        $query = User::query()->with('role');

        if ($search !== '') {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        if (
            in_array($status, [
                'awaiting_password',
                'pending_approval',
            ], true)
        ) {
            $query->where('status', 'pending')
                ->where('is_active', false)
                ->whereHas('role', function ($query) {
                    $query->whereIn('name', ['Doctor', 'Assistant']);
                });

            if ($status === 'awaiting_password') {
                $query->whereNull('password_set_at');
            } else {
                $query->whereNotNull('password_set_at');
            }
        } elseif ($status === 'active') {
            $query->where('status', 'active')
                ->where('is_active', true);
        } elseif ($status === 'suspended') {
            $query->where('status', 'suspended');
        }

        $users = $query
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $roles = Role::whereIn('name', ['Doctor', 'Assistant'])
            ->orderBy('name')
            ->get();
        return view('admin.users.index', compact(
            'users',
            'roles',
            'search',
            'status'
        ));
    }

    public function bulkApprove(Request $request): RedirectResponse
{
    Gate::authorize('manage-users');

    $data = $request->validate([
        'user_ids' => [
            'required',
            'array',
            'min:1',
            'max:100',
        ],
        'user_ids.*' => [
            'required',
            'integer',
            'distinct',
            'min:1',
        ],
    ], [
        'user_ids.required' => 'Select at least one account to approve.',
        'user_ids.max' => 'You can approve at most 100 accounts at once.',
    ]);

    $ids = array_map('intval', $data['user_ids']);

    $approvedCount = DB::transaction(function () use (
        $request,
        $ids
    ): int {
        $accounts = User::query()
            ->with('role')
            ->whereIn('id', $ids)
            ->orderBy('id')
            ->lockForUpdate()
            ->get();

        if ($accounts->count() !== count($ids)) {
            throw ValidationException::withMessages([
                'user_ids' => 'One or more accounts no longer exist. Refresh the list and try again.',
            ]);
        }

        // Validate every selected account before updating any.
        foreach ($accounts as $account) {
            $allowedRole = in_array(
                $account->role?->name,
                ['Doctor', 'Assistant'],
                true
            );

            if (
                ! $allowedRole ||
                $account->status !== 'pending' ||
                (bool) $account->is_active ||
                ! $account->password_set_at
            ) {
                throw ValidationException::withMessages([
                    'user_ids' => 'One or more selected accounts are no longer eligible. Refresh the list. No accounts were approved.',
                ]);
            }
        }

        $approvedAt = now();

        foreach ($accounts as $account) {
            // Keep the role already assigned to this account.
            $account->status = 'active';
            $account->is_active = true;
            $account->approved_by = $request->user()->id;
            $account->approved_at = $approvedAt;
            $account->save();
        }

        return $accounts->count();
    });

    return back()->with(
        'success',
        "{$approvedCount} account(s) approved successfully."
    );
}
    

    private function sendInvitation(User $user): bool
    {
        $token = Password::createToken($user);

        $url = route('account.setup.show', [
            'token' => $token,
            'email' => $user->email,
        ]);

        try {
            Mail::send(
                'emails.account-invitation',
                [
                    'user' => $user,
                    'url' => $url,
                ],
                function ($message) use ($user) {
                    $message->to($user->email)
                        ->subject(
                            'Set up your Fatty Liver Research account'
                        );
                }
            );

            return true;
        } catch (\Throwable $exception) {
            report($exception);
            return false;
        }
    }

    public function store(Request $request)
    {
        $this->adminOnly($request);

        $roleIds = DB::table('roles')
            ->whereIn('name', ['Doctor', 'Assistant'])
            ->pluck('id')
            ->all();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email'
            ],
            'role_id' => [
                'required',
                Rule::in($roleIds)
            ],
        ]);

        $user = new User();
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->role_id = $data['role_id'];
        $user->password = Hash::make(Str::random(64));
        $user->is_active = 0;
        $user->status = 'pending';
        $user->save();

        $sent = $this->sendInvitation($user);

        return redirect()
            ->route('admin.users.index')
            ->with(
                $sent ? 'success' : 'warning',
                $sent
                ? 'Account created. Invitation sent.'
                : 'Account created, but invitation delivery failed.'
            );
    }


    public function approve(Request $request, User $user)
    {
        $this->adminOnly($request);

        // Only pending accounts can be approved.
        abort_unless(
            $user->status === 'pending',
            422,
            'Only pending accounts can be approved.'
        );

        // Never modify another Admin through this workflow.
        $currentRole = DB::table('roles')
            ->where('id', $user->role_id)
            ->value('name');

        abort_if(
            $currentRole === 'Admin',
            403,
            'Admin accounts cannot be approved here.'
        );

        // Only Doctor and Assistant roles can be assigned.
        $allowedRoleIds = DB::table('roles')
            ->whereIn('name', ['Doctor', 'Assistant'])
            ->pluck('id')
            ->all();

        $validated = $request->validate([
            'role_id' => [
                'required',
                Rule::in($allowedRoleIds),
            ],
        ]);

        $user->role_id = $validated['role_id'];
        $user->status = 'active';
        $user->is_active = 1;
        $user->approved_by = $request->user()->id;
        $user->approved_at = now();

        $user->save();

        return redirect()
            ->route('admin.users.index')
            ->with(
                'success',
                'User approved with assigned role successfully.'
            );
    }


    public function suspend(Request $request, User $user)
    {
        $this->adminOnly($request);
        $this->permittedUser($user);

        abort_unless(
            $user->status === 'active',
            422,
            'Only active accounts can be suspended.'
        );

        $user->status = 'suspended';
        $user->is_active = 0;
        $user->save();

        return back()->with(
            'success',
            'Account suspended successfully.'
        );
    }

    public function reactivate(Request $request, User $user)
    {
        $this->adminOnly($request);
        $this->permittedUser($user);

        abort_unless(
            $user->status === 'suspended',
            422,
            'Only suspended accounts can be reactivated.'
        );

        $user->status = 'active';
        $user->is_active = 1;
        $user->approved_by = $request->user()->id;
        $user->approved_at = now();
        $user->save();

        return back()->with(
            'success',
            'Account reactivated successfully.'
        );
    }

    public function resendInvite(Request $request, User $user)
    {
        $this->adminOnly($request);
        $this->permittedUser($user);

        abort_unless(
            $user->status === 'pending',
            422,
            'Only pending accounts can receive invitations.'
        );

        $sent = $this->sendInvitation($user);

        return back()->with(
            $sent ? 'success' : 'warning',
            $sent
            ? 'Invitation sent.'
            : 'Invitation delivery failed.'
        );
    }
}
