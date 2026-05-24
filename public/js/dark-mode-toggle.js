/**
 * Dark Mode Toggle JavaScript
 * Handles theme switching with localStorage persistence
 */

(function() {
    'use strict';

    const THEME_KEY = 'asset-management-theme';
    const DARK_MODE_CLASS = 'dark-mode';
    const LIGHT_MODE_CLASS = 'light-mode';
    const SYSTEM_PREFERENCE = 'system';
    const DARK_PREFERENCE = 'dark';
    const LIGHT_PREFERENCE = 'light';

    /**
     * Get the user's theme preference
     */
    function getThemePreference() {
        const stored = localStorage.getItem(THEME_KEY);
        if (stored) {
            return stored;
        }
        // Default to system preference
        return SYSTEM_PREFERENCE;
    }

    /**
     * Check if system prefers dark mode
     */
    function systemPrefersDark() {
        return window.matchMedia('(prefers-color-scheme: dark)').matches;
    }

    /**
     * Determine the actual theme to apply
     */
    function getActualTheme(preference) {
        if (preference === SYSTEM_PREFERENCE) {
            return systemPrefersDark() ? DARK_PREFERENCE : LIGHT_PREFERENCE;
        }
        return preference;
    }

    /**
     * Apply theme to the DOM
     */
    function applyTheme(theme) {
        const root = document.documentElement;

        // Remove existing theme classes
        root.classList.remove(DARK_MODE_CLASS, LIGHT_MODE_CLASS);

        // Add the new theme class
        if (theme === DARK_PREFERENCE) {
            root.classList.add(DARK_MODE_CLASS);
            document.body.style.backgroundColor = '#0f172a';
            document.body.style.color = '#e2e8f0';
        } else {
            root.classList.add(LIGHT_MODE_CLASS);
            document.body.style.backgroundColor = '#ffffff';
            document.body.style.color = '#1e293b';
        }
    }

    /**
     * Initialize theme on page load
     */
    function initializeTheme() {
        const preference = getThemePreference();
        const actualTheme = getActualTheme(preference);
        applyTheme(actualTheme);
    }

    /**
     * Toggle theme
     */
    function toggleTheme() {
        const root = document.documentElement;
        const isDarkMode = root.classList.contains(DARK_MODE_CLASS);
        const newTheme = isDarkMode ? LIGHT_PREFERENCE : DARK_PREFERENCE;

        // Save preference
        localStorage.setItem(THEME_KEY, newTheme);

        // Apply theme
        applyTheme(newTheme);

        // Dispatch custom event for other parts of the app
        window.dispatchEvent(
            new CustomEvent('themeChange', {
                detail: { theme: newTheme }
            })
        );
    }

    /**
     * Handle system theme preference change
     */
    function handleSystemThemeChange(e) {
        const preference = getThemePreference();
        // Only apply system change if user hasn't set a preference
        if (preference === SYSTEM_PREFERENCE) {
            const actualTheme = e.matches ? DARK_PREFERENCE : LIGHT_PREFERENCE;
            applyTheme(actualTheme);
        }
    }

    /**
     * Setup event listeners
     */
    function setupEventListeners() {
        // Theme toggle button
        const themeToggleBtn = document.getElementById('themeToggleBtn');
        if (themeToggleBtn) {
            themeToggleBtn.addEventListener('click', toggleTheme);
        }

        // System theme preference change listener
        const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
        mediaQuery.addEventListener('change', handleSystemThemeChange);
    }

    /**
     * Clear theme preference (reset to system)
     */
    window.resetThemePreference = function() {
        localStorage.removeItem(THEME_KEY);
        initializeTheme();
    };

    /**
     * Set specific theme
     */
    window.setTheme = function(theme) {
        if ([DARK_PREFERENCE, LIGHT_PREFERENCE, SYSTEM_PREFERENCE].includes(theme)) {
            localStorage.setItem(THEME_KEY, theme);
            const actualTheme = getActualTheme(theme);
            applyTheme(actualTheme);
        }
    };

    /**
     * Get current theme
     */
    window.getCurrentTheme = function() {
        return getThemePreference();
    };

    // Initialize on DOM ready or immediately if DOM is already loaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            initializeTheme();
            setupEventListeners();
        });
    } else {
        initializeTheme();
        setupEventListeners();
    }
})();
