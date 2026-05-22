@extends('layouts.manager')

@section('content')
<div class="page-heading">
    <div>
        <p class="page-eyebrow">Logs</p>
        <h1>Tracking History</h1>
        <p class="breadcrumb-trail">
            <a href="{{ route('manager.dashboard') }}">Dashboard</a> / <span>History</span>
        </p>
    </div>
</div>

<div class="content-card">
    <div class="section-header">
        <div>
            <h2>Location logs per asset</h2>
            <p>Select an asset to view its full movement history.</p>
        </div>
    </div>
    <div class="table-wrap">
        <table class="app-table" data-datatable="true">
            <thead>
                <tr><th>Asset Code</th><th>Asset Name</th><th>Latest Location</th><th>Last Updated</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @foreach($assets as $asset)
                @php $latest = $asset->latestLocation; @endphp
                <tr>
                    <td>{{ $asset->asset_code }}</td>
                    <td>{{ $asset->name }}</td>
                    <td>@if($latest){{ $latest->latitude }}, {{ $latest->longitude }}@else No data @endif</td>
                    <td>{{ $latest ? $latest->last_recorded_at->diffForHumans() : '—' }}</td>
                    <td class="inline-actions">
                        <a href="{{ route('manager.tracking.asset-history', $asset) }}" class="btn btn-outline btn-sm">View full history</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection