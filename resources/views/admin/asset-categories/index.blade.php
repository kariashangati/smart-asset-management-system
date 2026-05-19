@extends('layouts.admin')

@section('title', 'Asset Categories')
@section('portal_label', 'Admin Portal')
@section('page_title', 'Asset Categories')
@section('dashboard_url', route('admin.dashboard'))

@section('content')
    <section class="content-card">
        <div class="section-header">
            <div>
                <h2>Asset Category Management</h2>
                <p>Create and maintain categories used to classify institutional assets.</p>
            </div>

            @can('asset_categories.create')
                <a href="{{ route('admin.asset-categories.create') }}" class="btn btn-primary">
                    Add Asset Category
                </a>
            @endcan
        </div>

        <div class="table-wrap">
            <table data-datatable="true" class="display app-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Category Name</th>
                        <th>Description</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($assetCategories as $assetCategory)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $assetCategory->name }}</td>
                            <td>{{ $assetCategory->description ?: '—' }}</td>
                            <td>{{ $assetCategory->created_at->format('d M Y') }}</td>
                            <td>
                                <div class="button-row">
                                    @can('asset_categories.update')
                                        <a
                                            href="{{ route('admin.asset-categories.edit', $assetCategory) }}"
                                            class="btn btn-outline"
                                        >
                                            Edit
                                        </a>
                                    @endcan

                                    @can('asset_categories.delete')
                                        <form
                                            method="POST"
                                            action="{{ route('admin.asset-categories.destroy', $assetCategory) }}"
                                            class="js-confirm-delete"
                                            data-title="Delete asset category?"
                                            data-text="This will permanently remove the selected asset category."
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