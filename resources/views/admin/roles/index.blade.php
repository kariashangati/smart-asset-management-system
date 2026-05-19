@extends('layouts.admin')

@section('title', 'Roles')
@section('portal_label', 'Admin Portal')
@section('page_title', 'Role Management')
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
                <h2>System Roles</h2>
                <p>Create roles and attach the exact permissions each role should have.</p>
            </div>

            @can('roles.create')
                <button type="button" class="btn btn-primary" data-modal-open="create-role-modal">
                    Add Role
                </button>
            @endcan
        </div>

        <div class="table-wrap">
            <table data-datatable="true" class="display app-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Role</th>
                        <th>Permissions</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($roles as $role)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <span class="badge badge-soft">{{ $role->name }}</span>
                            </td>
                            <td class="role-permission-summary">
                                <div class="tag-list">
                                    @forelse ($role->permissions->take(6) as $permission)
                                        <span class="badge badge-soft">{{ $permission->name }}</span>
                                    @empty
                                        <span class="badge badge-warning">No permissions</span>
                                    @endforelse

                                    @if ($role->permissions->count() > 6)
                                        <span class="badge badge-soft">
                                            +{{ $role->permissions->count() - 6 }} more
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td>{{ $role->created_at?->format('d M Y') ?: '—' }}</td>
                            <td>
                                <div class="inline-actions">
                                    @can('roles.update')
                                        <button
                                            type="button"
                                            class="btn btn-outline"
                                            data-modal-open="edit-role-modal-{{ $role->id }}"
                                        >
                                            Edit
                                        </button>
                                    @endcan

                                    @can('roles.delete')
                                        <form
                                            method="POST"
                                            action="{{ route('admin.roles.destroy', $role) }}"
                                            class="js-confirm-delete"
                                            data-title="Delete role?"
                                            data-text="This role will be permanently removed if it is not protected or assigned."
                                            data-confirm-text="Yes, delete role"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="btn btn-danger">
                                                Delete
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>

                        @can('roles.update')
                            <div class="app-modal" id="edit-role-modal-{{ $role->id }}">
                                <div class="modal-panel modal-wide">
                                    <div class="modal-header">
                                        <div>
                                            <h2>Edit Role</h2>
                                            <p>Update role name and permission assignments.</p>
                                        </div>

                                        <button type="button" class="icon-button" data-modal-close>&times;</button>
                                    </div>

                                    <form method="POST" action="{{ route('admin.roles.update', $role) }}">
                                        @csrf
                                        @method('PUT')

                                        <input type="hidden" name="_modal" value="edit-role-modal-{{ $role->id }}">

                                        <div class="modal-body">
                                            @if ($errors->any() && old('_modal') === 'edit-role-modal-'.$role->id)
                                                <div class="form-alert">
                                                    <strong>Please correct the following:</strong>
                                                    <ul>
                                                        @foreach ($errors->all() as $error)
                                                            <li>{{ $error }}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif

                                            <div class="form-group">
                                                <label for="edit_role_name_{{ $role->id }}">Role Name</label>
                                                <input
                                                    id="edit_role_name_{{ $role->id }}"
                                                    name="name"
                                                    type="text"
                                                    value="{{ old('_modal') === 'edit-role-modal-'.$role->id ? old('name') : $role->name }}"
                                                    required
                                                >
                                            </div>

                                            <br>

                                            <div class="form-group">
                                                <label>Permissions</label>

                                                <div class="permission-grid">
                                                    @foreach ($permissions as $permission)
                                                        <label class="permission-option">
                                                            <input
                                                                type="checkbox"
                                                                name="permissions[]"
                                                                value="{{ $permission->name }}"
                                                                @checked(
                                                                    old('_modal') === 'edit-role-modal-'.$role->id
                                                                        ? collect(old('permissions', []))->contains($permission->name)
                                                                        : $role->permissions->contains('name', $permission->name)
                                                                )
                                                            >
                                                            <span>{{ $permission->name }}</span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-outline" data-modal-close>
                                                Cancel
                                            </button>

                                            <button type="submit" class="btn btn-primary">
                                                Update Role
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endcan
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    @can('roles.create')
        <div class="app-modal" id="create-role-modal">
            <div class="modal-panel modal-wide">
                <div class="modal-header">
                    <div>
                        <h2>Create Role</h2>
                        <p>Define a role and choose its permissions.</p>
                    </div>

                    <button type="button" class="icon-button" data-modal-close>&times;</button>
                </div>

                <form method="POST" action="{{ route('admin.roles.store') }}">
                    @csrf

                    <input type="hidden" name="_modal" value="create-role-modal">

                    <div class="modal-body">
                        @if ($errors->any() && old('_modal') === 'create-role-modal')
                            <div class="form-alert">
                                <strong>Please correct the following:</strong>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="form-group">
                            <label for="create_role_name">Role Name</label>
                            <input
                                id="create_role_name"
                                name="name"
                                type="text"
                                value="{{ old('_modal') === 'create-role-modal' ? old('name') : '' }}"
                                placeholder="example_role"
                                required
                            >
                        </div>

                        <br>

                        <div class="form-group">
                            <label>Permissions</label>

                            <div class="permission-grid">
                                @foreach ($permissions as $permission)
                                    <label class="permission-option">
                                        <input
                                            type="checkbox"
                                            name="permissions[]"
                                            value="{{ $permission->name }}"
                                            @checked(
                                                old('_modal') === 'create-role-modal'
                                                    && collect(old('permissions', []))->contains($permission->name)
                                            )
                                        >
                                        <span>{{ $permission->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline" data-modal-close>
                            Cancel
                        </button>

                        <button type="submit" class="btn btn-primary">
                            Create Role
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endcan
@endsection