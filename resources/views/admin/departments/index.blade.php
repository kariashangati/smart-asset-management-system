@extends('layouts.admin')

@section('title', 'Departments')
@section('portal_label', 'Admin Portal')
@section('page_title', 'Departments')
@section('dashboard_url', route('admin.dashboard'))

@section('content')
    <section class="content-card">
        <div class="section-header">
            <div>
                <h2>Department Management</h2>
                <p>Create and maintain institutional departments that own or manage assets.</p>
            </div>

            @can('departments.create')
                <a href="{{ route('admin.departments.create') }}" class="btn btn-primary">
                    Add Department
                </a>
            @endcan
        </div>

        <div class="table-wrap">
            <table data-datatable="true" class="display app-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Department Name</th>
                        <th>Code</th>
                        <th>Description</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($departments as $department)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $department->name }}</td>
                            <td>{{ $department->code ?: '—' }}</td>
                            <td>{{ $department->description ?: '—' }}</td>
                            <td>{{ $department->created_at->format('d M Y') }}</td>
                            <td>
                                <div class="button-row">
                                    @can('departments.update')
                                        <a
                                            href="{{ route('admin.departments.edit', $department) }}"
                                            class="btn btn-outline"
                                        >
                                            Edit
                                        </a>
                                    @endcan

                                    @can('departments.delete')
                                        <form
                                            method="POST"
                                            action="{{ route('admin.departments.destroy', $department) }}"
                                            class="js-confirm-delete"
                                            data-title="Delete department?"
                                            data-text="This will permanently remove the selected department."
                                            data-confirm-text="Yes, delete it"
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
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endsection