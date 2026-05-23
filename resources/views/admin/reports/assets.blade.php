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
</div>

<div class="content-card">
    <div class="section-header">
        <div>
            <h2>All registered assets</h2>
            <p>Complete list of assets with category, department, and assigned tracker.</p>
        </div>
    </div>
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
                    <td>{{ $asset->asset_code }}</td>
                    <td>{{ $asset->name }}</td>
                    <td>{{ $asset->category->name ?? '—' }}</td>
                    <td>{{ $asset->department->name ?? '—' }}</td>
                    <td>{{ ucfirst($asset->status) }}</td>
                    <td>{{ $asset->activeAssignment->trackerDevice->device_name ?? 'Not assigned' }}</td>
                    <td>{{ $asset->purchase_date ? $asset->purchase_date->format('d M Y') : '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection