@props([
    'isToggleable' => true,
])

@if($isToggleable)
<div class="theme-toggle-wrapper">
    <button 
        id="themeToggleBtn" 
        class="theme-toggle-button" 
        title="Toggle dark/light mode"
        aria-label="Toggle dark/light mode"
    >
        <!-- Sun Icon (shown in dark mode) -->
        <svg id="sunIcon" class="theme-icon sun-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1m-16 0H1m15.364 1.636l.707.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>
        
        <!-- Moon Icon (shown in light mode) -->
        <svg id="moonIcon" class="theme-icon moon-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
        </svg>
    </button>
</div>
@endif

<style>
    .theme-toggle-wrapper {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .theme-toggle-button {
        background-color: transparent;
        border: 2px solid #475569;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        color: #cbd5e1;
    }

    .theme-toggle-button:hover {
        border-color: #3b82f6;
        color: #3b82f6;
        background-color: rgba(59, 130, 246, 0.1);
    }

    .theme-toggle-button:active {
        transform: scale(0.95);
    }

    .theme-icon {
        width: 20px;
        height: 20px;
        transition: opacity 0.3s ease, transform 0.3s ease;
    }

    .sun-icon {
        opacity: 1;
        transform: rotate(0deg);
    }

    .sun-icon.hidden {
        opacity: 0;
        transform: rotate(-180deg);
        display: none;
    }

    .moon-icon {
        opacity: 0;
        transform: rotate(180deg);
        display: none;
    }

    .moon-icon.hidden {
        opacity: 0;
        display: none;
    }

    .moon-icon.active {
        opacity: 1;
        transform: rotate(0deg);
        display: block;
    }

    /* Dark mode: show moon icon, hide sun icon */
    :root.dark-mode .sun-icon {
        opacity: 0;
        transform: rotate(180deg);
        display: none;
    }

    :root.dark-mode .moon-icon {
        opacity: 1;
        transform: rotate(0deg);
        display: block;
    }

    /* Light mode: show sun icon, hide moon icon */
    :root.light-mode .sun-icon {
        opacity: 1;
        transform: rotate(0deg);
        display: block;
    }

    :root.light-mode .moon-icon {
        opacity: 0;
        transform: rotate(180deg);
        display: none;
    }
</style>
