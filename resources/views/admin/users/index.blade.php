@extends('layouts.admin')

@section('title', 'Users')
@section('portal_label', 'Admin Portal')
@section('page_title', 'User Management')
@section('dashboard_url', route('admin.dashboard'))

@section('content')
    @if ($errors->any() && old('_modal'))
        <script>
            window.defaultOpenModal = @json(old('_modal'));
        </script>
    @endif

    <section class="content-card">
        <div class="section-header">
            <div>
                <h2>System Users</h2>
                <p>Create users, assign roles, and activate or deactivate access.</p>
            </div>

            @can('users.create')
                <button type="button" class="btn btn-primary" data-modal-open="create-user-modal">
                    Add User
                </button>
            @endcan
        </div>

        <div class="table-wrap">
            <table data-datatable="true" class="display app-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>User</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Department</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->phone ?: '—' }}</td>
                            <td>
                                <div class="tag-list">
                                    @forelse ($user->getRoleNames() as $role)
                                        <span class="badge badge-soft">{{ $role }}</span>
                                    @empty
                                        <span class="badge badge-warning">No role</span>
                                    @endforelse
                                </div>
                            </td>
                            <td>
                                @if ($user->isDepartmentManager() && $user->department)
                                    <span class="badge badge-info">{{ $user->department->name }}</span>
                                @elseif ($user->isAdmin())
                                    <span class="badge badge-soft">All Departments</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if ($user->isActive())
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-warning">Inactive</span>
                                @endif
                            </td>
                            <td>{{ $user->created_at->format('d M Y') }}</td>
                            <td>
                                <div class="inline-actions">
                                    @can('users.view')
                                        <a
                                            href="{{ route('admin.users.show', $user) }}"
                                            class="btn btn-outline"
                                        >
                                            View
                                        </a>
                                    @endcan

                                    @can('users.update')
                                        <button
                                            type="button"
                                            class="btn btn-outline"
                                            data-modal-open="edit-user-modal-{{ $user->id }}"
                                        >
                                            Edit
                                        </button>
                                    @endcan

                                    @can('users.update')
                                        @if (auth()->id() !== $user->id)
                                            <form
                                                method="POST"
                                                action="{{ route('admin.users.toggle-status', $user) }}"
                                                class="js-confirm-delete"
                                                data-title="{{ $user->isActive() ? 'Deactivate user?' : 'Activate user?' }}"
                                                data-text="{{ $user->isActive() ? 'This user will no longer be able to access the system.' : 'This user will regain access to the system.' }}"
                                                data-confirm-text="{{ $user->isActive() ? 'Yes, deactivate' : 'Yes, activate' }}"
                                            >
                                                @csrf
                                                @method('PATCH')

                                                <button
                                                    type="submit"
                                                    class="btn {{ $user->isActive() ? 'btn-danger' : 'btn-primary' }}"
                                                >
                                                    {{ $user->isActive() ? 'Deactivate' : 'Activate' }}
                                                </button>
                                            </form>
                                        @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>

                        @can('users.update')
                            <div class="app-modal" id="edit-user-modal-{{ $user->id }}">
                                <div class="modal-panel modal-wide">
                                    <div class="modal-header">
                                        <div>
                                            <h2>Edit User</h2>
                                            <p>Update account details, status, role, or department assignment.</p>
                                        </div>

                                        <button type="button" class="icon-button" data-modal-close>&times;</button>
                                    </div>

                                    <form method="POST" action="{{ route('admin.users.update', $user) }}">
                                        @csrf
                                        @method('PUT')

                                        <input type="hidden" name="_modal" value="edit-user-modal-{{ $user->id }}">

                                        <div class="modal-body">
                                            <div class="form-grid">
                                                <div class="form-group">
                                                    <label for="edit_name_{{ $user->id }}">Full Name</label>
                                                    <input
                                                        id="edit_name_{{ $user->id }}"
                                                        name="name"
                                                        type="text"
                                                        value="{{ old('_modal') === 'edit-user-modal-'.$user->id ? old('name') : $user->name }}"
                                                        required
                                                    >
                                                </div>

                                                <div class="form-group">
                                                    <label for="edit_email_{{ $user->id }}">Email Address</label>
                                                    <input
                                                        id="edit_email_{{ $user->id }}"
                                                        name="email"
                                                        type="email"
                                                        value="{{ old('_modal') === 'edit-user-modal-'.$user->id ? old('email') : $user->email }}"
                                                        required
                                                    >
                                                </div>

                                                <div class="form-group">
                                                    <label for="edit_phone_{{ $user->id }}">Phone</label>
                                                    <input
                                                        id="edit_phone_{{ $user->id }}"
                                                        name="phone"
                                                        type="text"
                                                        value="{{ old('_modal') === 'edit-user-modal-'.$user->id ? old('phone') : $user->phone }}"
                                                    >
                                                </div>

                                                <div class="form-group">
                                                    <label for="edit_status_{{ $user->id }}">Status</label>
                                                    <select id="edit_status_{{ $user->id }}" name="status" required>
                                                        @foreach (\App\Models\User::STATUSES as $status)
                                                            <option
                                                                value="{{ $status }}"
                                                                @selected((old('_modal') === 'edit-user-modal-'.$user->id ? old('status') : $user->status) === $status)
                                                            >
                                                                {{ ucfirst($status) }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="form-group field-span-2">
                                                    <label for="edit_role_{{ $user->id }}">Role</label>
                                                    <select id="edit_role_{{ $user->id }}" name="role" required>
                                                        @foreach ($roles as $role)
                                                            <option
                                                                value="{{ $role->name }}"
                                                                @selected(
                                                                    (old('_modal') === 'edit-user-modal-'.$user->id
                                                                        ? old('role')
                                                                        : $user->getRoleNames()->first()) === $role->name
                                                                )
                                                            >
                                                                {{ $role->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="form-group field-span-2" id="edit_department_field_{{ $user->id }}" style="display: none;">
                                                    <label for="edit_department_{{ $user->id }}">
                                                        Department
                                                        <span class="text-required">*</span>
                                                        <small class="form-hint">(Required for Asset Managers)</small>
                                                    </label>
                                                    <select id="edit_department_{{ $user->id }}" name="department_id">
                                                        <option value="">Select department</option>
                                                        @foreach ($departments as $department)
                                                            <option
                                                                value="{{ $department->id }}"
                                                                @selected(
                                                                    (old('_modal') === 'edit-user-modal-'.$user->id
                                                                        ? old('department_id')
                                                                        : $user->department_id) == $department->id
                                                                )
                                                            >
                                                                {{ $department->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('department_id')
                                                        <span class="form-error">{{ $message }}</span>
                                                    @enderror
                                                </div>

                                                <div class="form-group">
                                                    <label for="edit_password_{{ $user->id }}">New Password</label>
                                                    <input
                                                        id="edit_password_{{ $user->id }}"
                                                        name="password"
                                                        type="password"
                                                    >
                                                </div>

                                                <div class="form-group">
                                                    <label for="edit_password_confirmation_{{ $user->id }}">Confirm New Password</label>
                                                    <input
                                                        id="edit_password_confirmation_{{ $user->id }}"
                                                        name="password_confirmation"
                                                        type="password"
                                                    >
                                                </div>
                                            </div>
                                        </div>

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-outline" data-modal-close>
                                                Cancel
                                            </button>

                                            <button type="submit" class="btn btn-primary">
                                                Update User
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <script>
                                (function() {
                                    const roleSelect = document.getElementById('edit_role_{{ $user->id }}');
                                    const deptField = document.getElementById('edit_department_field_{{ $user->id }}');
                                    const deptSelect = document.getElementById('edit_department_{{ $user->id }}');
                                    
                                    function toggleDepartmentField() {
                                        if (roleSelect.value === 'asset_manager') {
                                            deptField.style.display = 'block';
                                            deptSelect.required = true;
                                        } else {
                                            deptField.style.display = 'none';
                                            deptSelect.required = false;
                                        }
                                    }
                                    
                                    roleSelect.addEventListener('change', toggleDepartmentField);
                                    toggleDepartmentField();
                                })();
                            </script>
                        @endcan
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    @can('users.create')
        <div class="app-modal" id="create-user-modal">
            <div class="modal-panel modal-wide">
                <div class="modal-header">
                    <div>
                        <h2>Create User</h2>
                        <p>Add a user and assign the correct system role and department.</p>
                    </div>

                    <button type="button" class="icon-button" data-modal-close>&times;</button>
                </div>

                <form method="POST" action="{{ route('admin.users.store') }}">
                    @csrf

                    <input type="hidden" name="_modal" value="create-user-modal">

                    <div class="modal-body">
                        @if ($errors->any() && old('_modal') === 'create-user-modal')
                            <div class="form-alert">
                                <strong>Please correct the following:</strong>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="form-grid">
                            <div class="form-group">
                                <label for="create_name">Full Name</label>
                                <input
                                    id="create_name"
                                    name="name"
                                    type="text"
                                    value="{{ old('_modal') === 'create-user-modal' ? old('name') : '' }}"
                                    required
                                >
                            </div>

                            <div class="form-group">
                                <label for="create_email">Email Address</label>
                                <input
                                    id="create_email"
                                    name="email"
                                    type="email"
                                    value="{{ old('_modal') === 'create-user-modal' ? old('email') : '' }}"
                                    required
                                >
                            </div>

                            <div class="form-group">
                                <label for="create_phone">Phone</label>
                                <input
                                    id="create_phone"
                                    name="phone"
                                    type="text"
                                    value="{{ old('_modal') === 'create-user-modal' ? old('phone') : '' }}"
                                >
                            </div>

                            <div class="form-group">
                                <label for="create_status">Status</label>
                                <select id="create_status" name="status" required>
                                    @foreach (\App\Models\User::STATUSES as $status)
                                        <option
                                            value="{{ $status }}"
                                            @selected((old('_modal') === 'create-user-modal' ? old('status', 'active') : 'active') === $status)
                                        >
                                            {{ ucfirst($status) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group field-span-2">
                                <label for="create_role">Role</label>
                                <select id="create_role" name="role" required>
                                    <option value="">Select role</option>
                                    @foreach ($roles as $role)
                                        <option
                                            value="{{ $role->name }}"
                                            @selected(old('_modal') === 'create-user-modal' && old('role') === $role->name)
                                        >
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group field-span-2" id="create_department_field" style="display: none;">
                                <label for="create_department">
                                    Department
                                    <span class="text-required">*</span>
                                    <small class="form-hint">(Required for Asset Managers)</small>
                                </label>
                                <select id="create_department" name="department_id">
                                    <option value="">Select department</option>
                                    @foreach ($departments as $department)
                                        <option
                                            value="{{ $department->id }}"
                                            @selected(old('_modal') === 'create-user-modal' && old('department_id') == $department->id)
                                        >
                                            {{ $department->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('department_id')
                                    <span class="form-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="create_password">Password</label>
                                <input
                                    id="create_password"
                                    name="password"
                                    type="password"
                                    required
                                >
                            </div>

                            <div class="form-group">
                                <label for="create_password_confirmation">Confirm Password</label>
                                <input
                                    id="create_password_confirmation"
                                    name="password_confirmation"
                                    type="password"
                                    required
                                >
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline" data-modal-close>
                            Cancel
                        </button>

                        <button type="submit" class="btn btn-primary">
                            Create User
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            (function() {
                const roleSelect = document.getElementById('create_role');
                const deptField = document.getElementById('create_department_field');
                const deptSelect = document.getElementById('create_department');
                
                function toggleDepartmentField() {
                    if (roleSelect.value === 'asset_manager') {
                        deptField.style.display = 'block';
                        deptSelect.required = true;
                    } else {
                        deptField.style.display = 'none';
                        deptSelect.required = false;
                    }
                }
                
                roleSelect.addEventListener('change', toggleDepartmentField);
                toggleDepartmentField();
            })();
        </script>
    @endcan
@endsection
