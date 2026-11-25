@extends('layouts.patients')

@section('title', 'User Management')

@section('content')
    <header class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h1 class="h2 fw-bold mb-1">User Management</h1>

            <p class="text-secondary mb-0">
                {{ $users->total() }} matching accounts
            </p>
        </div>

        <a href="{{ route('dashboard') }}"
           class="btn btn-outline-secondary">
            Dashboard
        </a>
    </header>

    {{-- The layout already displays success and validation messages. --}}
    @if (session('warning'))
        <div class="alert alert-warning" role="alert">
            {{ session('warning') }}
        </div>
    @endif

    {{-- Create an invited account --}}
    <section class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <h2 class="h5 mb-3">Invite User</h2>

            <form method="POST"
                  action="{{ route('admin.users.store') }}">
                @csrf

                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="name" class="form-label">
                            Full Name
                        </label>

                        <input id="name"
                               name="name"
                               type="text"
                               class="form-control"
                               value="{{ old('name') }}"
                               maxlength="255"
                               autocomplete="name"
                               required>
                    </div>

                    <div class="col-md-4">
                        <label for="email" class="form-label">
                            Email Address
                        </label>

                        <input id="email"
                               name="email"
                               type="email"
                               class="form-control"
                               value="{{ old('email') }}"
                               maxlength="255"
                               autocomplete="email"
                               required>
                    </div>

                    <div class="col-md-2">
                        <label for="role_id" class="form-label">
                            Role
                        </label>

                        <select id="role_id"
                                name="role_id"
                                class="form-select"
                                required>
                            <option value="">Select role</option>

                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}"
                                        @selected((string) old('role_id') === (string) $role->id)>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <button type="submit"
                                class="btn btn-primary w-100">
                            Send Invitation
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </section>

    {{-- Search and status filters --}}
    <form method="GET"
          action="{{ route('admin.users.index') }}"
          class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <div class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label for="search" class="form-label">
                        Search Accounts
                    </label>

                    <input id="search"
                           name="search"
                           type="search"
                           class="form-control"
                           value="{{ $search }}"
                           maxlength="255"
                           placeholder="Name or email">
                </div>

                <div class="col-md-4">
                    <label for="status" class="form-label">
                        Account Filter
                    </label>

                    <select id="status"
                            name="status"
                            class="form-select">
                        <option value="all"
                                @selected($status === 'all')>
                            All Accounts
                        </option>

                        <option value="awaiting_password"
                                @selected($status === 'awaiting_password')>
                            Awaiting Password
                        </option>

                        <option value="pending_approval"
                                @selected($status === 'pending_approval')>
                            Pending Approval
                        </option>

                        <option value="active"
                                @selected($status === 'active')>
                            Active
                        </option>

                        <option value="suspended"
                                @selected($status === 'suspended')>
                            Suspended
                        </option>
                    </select>
                </div>

                <div class="col-md-3">
                    <div class="d-flex gap-2">
                        <button type="submit"
                                class="btn btn-primary">
                            Apply
                        </button>

                        <a href="{{ route('admin.users.index') }}"
                           class="btn btn-outline-secondary">
                            Reset
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>

    {{-- This form is separate from individual row forms. --}}
    <form id="bulk-approval-form"
          method="POST"
          action="{{ route('admin.users.approve-selected') }}"
          class="mb-3"
          onsubmit="return confirm('Approve the selected accounts with their currently assigned roles?');">
        @csrf

        <div class="d-flex align-items-center flex-wrap gap-3">
            <button type="submit" class="btn btn-success">
                Approve Selected
            </button>

            <span class="text-secondary small">
                Selection applies to this page only.
                Only accounts ready for approval can be selected.
            </span>
        </div>
    </form>

    <section class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th scope="col">
                                <input id="select-all"
                                       type="checkbox"
                                       class="form-check-input"
                                       aria-label="Select all eligible accounts on this page">
                            </th>

                            <th scope="col">Name</th>
                            <th scope="col">Email</th>
                            <th scope="col">Role</th>
                            <th scope="col">Password Setup</th>
                            <th scope="col">Account Status</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($users as $account)
                            @php
                                $isStaff = in_array(
                                    $account->role?->name,
                                    ['Doctor', 'Assistant'],
                                    true
                                );

                                $isPending = $account->status === 'pending'
                                    && ! $account->is_active;

                                $canApprove = $isStaff
                                    && $isPending
                                    && $account->password_set_at !== null;
                            @endphp

                            <tr>
                                <td>
                                    @if ($canApprove)
                                        <input type="checkbox"
                                               name="user_ids[]"
                                               value="{{ $account->id }}"
                                               form="bulk-approval-form"
                                               class="form-check-input account-checkbox"
                                               aria-label="Select {{ $account->name }} for approval">
                                    @endif
                                </td>

                                <td>{{ $account->name }}</td>
                                <td>{{ $account->email }}</td>
                                <td>{{ $account->role?->name ?? 'Unassigned' }}</td>

                                <td>
                                    @if ($account->password_set_at)
                                        <span class="badge text-bg-success">
                                            Completed
                                        </span>
                                    @elseif ($isPending)
                                        <span class="badge text-bg-warning">
                                            Awaiting Password
                                        </span>
                                    @else
                                        <span class="text-secondary">
                                            Not recorded
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    @if ($isPending)
                                        @if ($account->password_set_at)
                                            <span class="badge text-bg-info">
                                                Pending Approval
                                            </span>
                                        @else
                                            <span class="badge text-bg-warning">
                                                Awaiting Password
                                            </span>
                                        @endif
                                    @elseif ($account->status === 'active' && $account->is_active)
                                        <span class="badge text-bg-success">
                                            Active
                                        </span>
                                    @elseif ($account->status === 'suspended')
                                        <span class="badge text-bg-danger">
                                            Suspended
                                        </span>
                                    @else
                                        <span class="badge text-bg-secondary">
                                            Review Required
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    <div class="d-flex flex-wrap gap-2">
                                        @if (! $isStaff)
                                            <span class="text-secondary small">
                                                Managed separately
                                            </span>

                                        @elseif ($canApprove)
                                            <form method="POST"
                                                  action="{{ route('admin.users.approve', $account) }}"
                                                  onsubmit="return confirm('Approve this account with the selected role?');">
                                                @csrf
                                                @method('PATCH')

                                                <div class="d-flex gap-2">
                                                    <select name="role_id"
                                                            class="form-select form-select-sm"
                                                            aria-label="Role for {{ $account->name }}"
                                                            required>
                                                        @foreach ($roles as $role)
                                                            <option value="{{ $role->id }}"
                                                                    @selected((int) $account->role_id === (int) $role->id)>
                                                                {{ $role->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>

                                                    <button type="submit"
                                                            class="btn btn-success btn-sm">
                                                        Approve
                                                    </button>
                                                </div>
                                            </form>

                                        @elseif ($isPending)
                                            <form method="POST"
                                                  action="{{ route('admin.users.invite', $account) }}">
                                                @csrf

                                                <button type="submit"
                                                        class="btn btn-outline-primary btn-sm">
                                                    Resend Invitation
                                                </button>
                                            </form>

                                        @elseif ($account->status === 'active' && $account->is_active)
                                            <form method="POST"
                                                  action="{{ route('admin.users.suspend', $account) }}"
                                                  onsubmit="return confirm('Suspend this account?');">
                                                @csrf
                                                @method('PATCH')

                                                <button type="submit"
                                                        class="btn btn-outline-danger btn-sm">
                                                    Suspend
                                                </button>
                                            </form>

                                        @elseif ($account->status === 'suspended')
                                            <form method="POST"
                                                  action="{{ route('admin.users.reactivate', $account) }}"
                                                  onsubmit="return confirm('Reactivate this account?');">
                                                @csrf
                                                @method('PATCH')

                                                <button type="submit"
                                                        class="btn btn-outline-success btn-sm">
                                                    Reactivate
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    No accounts match your search and filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($users->hasPages())
                <div class="mt-3">
                    {{ $users->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </section>

    <script>
        const selectAll = document.getElementById('select-all');
        const accountCheckboxes = Array.from(
            document.querySelectorAll('.account-checkbox')
        );

        function updateSelectAll() {
            const selectedCount = accountCheckboxes.filter(
                checkbox => checkbox.checked
            ).length;

            selectAll.disabled = accountCheckboxes.length === 0;

            selectAll.checked = accountCheckboxes.length > 0
                && selectedCount === accountCheckboxes.length;

            selectAll.indeterminate = selectedCount > 0
                && selectedCount < accountCheckboxes.length;
        }

        selectAll.addEventListener('change', function () {
            accountCheckboxes.forEach(checkbox => {
                checkbox.checked = selectAll.checked;
            });

            updateSelectAll();
        });

        accountCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectAll);
        });

        updateSelectAll();
    </script>
@endsection