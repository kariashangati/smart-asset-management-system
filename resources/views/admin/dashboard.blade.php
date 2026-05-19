@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('portal_label', 'Admin Portal')
@section('page_title', 'Dashboard Overview')
@section('dashboard_url', route('admin.dashboard'))

@section('content')
    <section class="stat-grid">
        <article class="stat-card">
            <span>Total Users</span>
            <strong>2</strong>
            <small>Seeded system accounts</small>
        </article>

        <article class="stat-card">
            <span>Portal Status</span>
            <strong>Ready</strong>
            <small>Authorization completed</small>
        </article>

        <article class="stat-card">
            <span>Frontend Utilities</span>
            <strong>Phase 3</strong>
            <small>SweetAlert2 and DataTables</small>
        </article>
    </section>

    <section class="content-card">
        <div class="section-header">
            <div>
                <h2>Frontend Utility Verification</h2>
                <p>Use these actions to confirm SweetAlert2 and delete confirmation behavior.</p>
            </div>
        </div>

        <div class="button-row">
            <form method="POST" action="{{ route('admin.ui-test.flash') }}">
                @csrf
                <button type="submit" class="btn btn-primary">
                    Test Success Notification
                </button>
            </form>

            <form
                method="POST"
                action="{{ route('admin.ui-test.delete') }}"
                class="js-confirm-delete"
                data-title="Confirm test action"
                data-text="This only tests the SweetAlert2 confirmation modal. No real data will be deleted."
                data-confirm-text="Yes, test it"
            >
                @csrf
                @method('DELETE')

                <button type="submit" class="btn btn-danger">
                    Test Delete Confirmation
                </button>
            </form>
        </div>
    </section>

    <section class="content-card">
        <div class="section-header">
            <div>
                <h2>DataTables Verification</h2>
                <p>This temporary sample table confirms searching, sorting, and pagination.</p>
            </div>
        </div>

        <div class="table-wrap">
            <table data-datatable="true" class="display app-table">
                <thead>
                    <tr>
                        <th>Module</th>
                        <th>Status</th>
                        <th>Phase</th>
                        <th>Purpose</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Authentication</td>
                        <td>Complete</td>
                        <td>Phase 1</td>
                        <td>Laravel 12 and Fortify setup</td>
                    </tr>
                    <tr>
                        <td>Authorization</td>
                        <td>Complete</td>
                        <td>Phase 2</td>
                        <td>Roles, permissions, and portal routing</td>
                    </tr>
                    <tr>
                        <td>SweetAlert2</td>
                        <td>Testing</td>
                        <td>Phase 3</td>
                        <td>Flash notifications and confirmation modals</td>
                    </tr>
                    <tr>
                        <td>DataTables</td>
                        <td>Testing</td>
                        <td>Phase 3</td>
                        <td>Search, sorting, and pagination</td>
                    </tr>
                    <tr>
                        <td>Departments</td>
                        <td>Planned</td>
                        <td>Phase 4</td>
                        <td>Administrative setup module</td>
                    </tr>
                    <tr>
                        <td>Assets</td>
                        <td>Planned</td>
                        <td>Phase 6</td>
                        <td>Core institutional asset records</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
@endsection