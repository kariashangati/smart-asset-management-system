@props([
    'icon' => 'inbox',
    'title' => 'No data found',
    'description' => 'There is no data to display.',
    'action' => null,
    'actionText' => 'Create new',
])

<div class="empty-state">
    <div class="empty-state-content">
        @switch($icon)
            @case('inbox')
                <svg class="empty-state-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
                @break
            @case('chart')
                <svg class="empty-state-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                @break
            @case('alert')
                <svg class="empty-state-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                @break
            @case('document')
                <svg class="empty-state-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                @break
            @case('location')
                <svg class="empty-state-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                @break
            @default
                <svg class="empty-state-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
        @endswitch

        <h3 class="empty-state-title">{{ $title }}</h3>
        <p class="empty-state-description">{{ $description }}</p>

        @if($action)
            <a href="{{ $action }}" class="empty-state-action">
                <svg class="empty-state-action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                {{ $actionText }}
            </a>
        @endif
    </div>
</div>

<style>
    .empty-state {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 300px;
        padding: 3rem 1rem;
        border-radius: 0.5rem;
        background-color: rgba(15, 23, 42, 0.5);
    }

    .empty-state-content {
        text-align: center;
    }

    .empty-state-icon {
        width: 4rem;
        height: 4rem;
        color: #64748b;
        margin: 0 auto 1rem;
        opacity: 0.5;
    }

    .empty-state-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #e2e8f0;
        margin-bottom: 0.5rem;
    }

    .empty-state-description {
        color: #94a3b8;
        margin-bottom: 1.5rem;
        font-size: 0.95rem;
    }

    .empty-state-action {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        background-color: #3b82f6;
        color: white;
        border-radius: 0.375rem;
        text-decoration: none;
        font-weight: 500;
        transition: background-color 0.2s ease;
    }

    .empty-state-action:hover {
        background-color: #2563eb;
    }

    .empty-state-action-icon {
        width: 1.25rem;
        height: 1.25rem;
    }
</style>
