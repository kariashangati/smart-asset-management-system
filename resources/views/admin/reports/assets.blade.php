@extends('layouts.admin')

@section('content')
<div class="page-heading">
    <div>
        <p class="page-eyebrow">Reports</p>
        <h1>Asset Report</h1>
        <p class="breadcrumb-trail">
            <a href="{{ route('admin.dashboard') }}">Dashboard</a> / <span>Asset Report</span>
        </p>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.reports.assets') }}?export=pdf" class="btn btn-primary" download>
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
            </svg>
            <span>Download PDF</span>
        </a>
    </div>
</div>

<div class="content-card">
    <div class="section-header">
        <div>
            <h2>All registered assets</h2>
            <p>Complete list of assets with category, department, and assigned tracker.</p>
        </div>
    </div>

    @if($assets->isEmpty())
        <x-empty-state 
            icon="inbox"
            title="No Assets Found"
            description="There are no assets registered in the system yet. Start by creating your first asset."
            action="{{ route('admin.assets.create') }}"
            actionText="Create First Asset"
        />
    @else
        <div class="table-wrap">
            <table class="app-table" data-datatable="true">
                <thead>
                    <tr>
                        <th>Asset Code</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Department</th>
                        <th>Status</th>
                        <th>Assigned Device</th>
                        <th>Purchase Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($assets as $asset)
                    <tr>
                        <td><span class="badge badge-soft">{{ $asset->asset_code }}</span></td>
                        <td>
                            <a href="{{ route('admin.assets.show', $asset) }}" class="link-primary">
                                {{ $asset->name }}
                            </a>
                        </td>
                        <td>{{ $asset->category->name ?? '—' }}</td>
                        <td>{{ $asset->department->name ?? '—' }}</td>
                        <td>
                            <span class="badge {{ $asset->status === 'active' ? 'badge-success' : ($asset->status === 'maintenance' ? 'badge-warning' : 'badge-danger') }}">
                                {{ ucfirst($asset->status) }}
                            </span>
                        </td>
                        <td>{{ $asset->activeAssignment->trackerDevice->device_name ?? 'Not assigned' }}</td>
                        <td>{{ $asset->purchase_date ? $asset->purchase_date->format('d M Y') : '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<style>
    .page-actions {
        display: flex;
        gap: 0.75rem;
        align-items: center;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        font-weight: 500;
        text-decoration: none;
        transition: background-color 0.2s ease;
    }

    .btn-primary {
        background-color: #3b82f6;
        color: white;
    }

    .btn-primary:hover {
        background-color: #2563eb;
    }

    .h-5 {
        width: 1.25rem;
        height: 1.25rem;
    }

    .link-primary {
        color: #3b82f6;
        text-decoration: none;
        font-weight: 500;
    }

    .link-primary:hover {
        text-decoration: underline;
    }

    .badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.875rem;
        font-weight: 500;
    }

    .badge-soft {
        background-color: rgba(100, 116, 139, 0.1);
        color: #cbd5e1;
    }

    .badge-success {
        background-color: rgba(34, 197, 94, 0.1);
        color: #86efac;
    }

    .badge-warning {
        background-color: rgba(251, 146, 60, 0.1);
        color: #fb923c;
    }

    .badge-danger {
        background-color: rgba(239, 68, 68, 0.1);
        color: #f87171;
    }
</style>
@endsection
