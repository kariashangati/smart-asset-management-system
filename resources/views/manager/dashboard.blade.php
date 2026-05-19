@extends('layouts.manager')

@section('title', 'Asset Manager Dashboard')
@section('portal_label', 'Asset Manager Portal')
@section('page_title', 'Dashboard Overview')
@section('dashboard_url', route('manager.dashboard'))

@section('content')
    <section class="stat-grid">
        <article class="stat-card">
            <span>Managed Assets</span>
            <strong>0</strong>
            <small>Asset module comes later</small>
        </article>

        <article class="stat-card">
            <span>Tracking</span>
            <strong>Ready</strong>
            <small>Map pages come in tracking phase</small>
        </article>

        <article class="stat-card">
            <span>Alert Center</span>
            <strong>Planned</strong>
            <small>Alert workflow arrives later</small>
        </article>
    </section>

    <section class="content-card">
        <div class="section-header">
            <div>
                <h2>Manager Portal Foundation</h2>
                <p>
                    This dashboard now uses the reusable manager layout,
                    shared topbar, footer, and breadcrumbs.
                </p>
            </div>
        </div>
    </section>
@endsection