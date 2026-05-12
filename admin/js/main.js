document.addEventListener('DOMContentLoaded', function () {

    // ── Theme Toggle ──
    const themeSwitch = document.getElementById('themeSwitch');
    const storedTheme = localStorage.getItem('theme');
    const systemDark  = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const initTheme   = storedTheme || (systemDark ? 'dark' : 'light');

    document.documentElement.setAttribute('data-bs-theme', initTheme);
    if (themeSwitch) {
        themeSwitch.checked = (initTheme === 'dark');
        themeSwitch.addEventListener('change', function () {
            const t = this.checked ? 'dark' : 'light';
            document.documentElement.setAttribute('data-bs-theme', t);
            localStorage.setItem('theme', t);
        });
    }

    // ── Sidebar Toggle ──
    const toggle  = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.querySelector('.sidebar-overlay');

    // Restore desktop state
    if (window.innerWidth >= 768 && localStorage.getItem('sidebarHidden') === 'true') {
        sidebar.classList.add('sidebar-hidden');
    }

    function openSidebar() {
        sidebar.classList.remove('sidebar-hidden');
        sidebar.classList.add('show');
        if (overlay) overlay.classList.add('show');
        document.body.classList.add('sidebar-open');
    }

    function closeSidebar() {
        sidebar.classList.remove('show');
        if (overlay) overlay.classList.remove('show');
        document.body.classList.remove('sidebar-open');
    }

    function toggleDesktop() {
        const hidden = sidebar.classList.toggle('sidebar-hidden');
        localStorage.setItem('sidebarHidden', hidden);
    }

    if (toggle) {
        toggle.addEventListener('click', function (e) {
            e.stopPropagation();
            if (window.innerWidth >= 768) {
                toggleDesktop();
            } else {
                sidebar.classList.contains('show') ? closeSidebar() : openSidebar();
            }
        });
    }

    if (overlay) overlay.addEventListener('click', closeSidebar);

    // Close on mobile nav click
    document.querySelectorAll('#sidebar .nav-link:not([data-bs-toggle])').forEach(link => {
        link.addEventListener('click', function () {
            if (window.innerWidth < 768) closeSidebar();
        });
    });

    // Clean up on resize to desktop
    window.addEventListener('resize', function () {
        if (window.innerWidth >= 768) {
            closeSidebar();
            // Re-apply desktop hidden state
            if (localStorage.getItem('sidebarHidden') === 'true') {
                sidebar.classList.add('sidebar-hidden');
            }
        }
    });

    // Escape key closes mobile sidebar
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeSidebar();
    });
});