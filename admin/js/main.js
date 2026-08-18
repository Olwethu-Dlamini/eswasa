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

    // ── Sidebar submenus: accordion + persistence + scroll-into-view ──
    const SUBMENU_KEY = 'sidebarOpenSubmenus';

    function getOpenMenus() {
        try { return JSON.parse(localStorage.getItem(SUBMENU_KEY) || '[]'); }
        catch (e) { return []; }
    }
    function saveOpenMenus(list) {
        localStorage.setItem(SUBMENU_KEY, JSON.stringify(list));
    }

    const submenus = document.querySelectorAll('#sidebar ul[id^="submenu-"]');

    // 1. Restore persisted open state.
    //
    // PHP already marks the current page's group open. Restoring remembered
    // menus on top of that could leave two open at once, which contradicts the
    // accordion below — it allows exactly one. The active group wins: if PHP
    // opened one, nothing is restored, so the sidebar always loads in a state
    // the accordion could itself produce.
    // See docs/superpowers/specs/2026-08-18-cms-batch-c-design.md (C5).
    const activeMenu = Array.prototype.find.call(submenus, m => m.classList.contains('show'));
    if (!activeMenu) {
        const remembered = getOpenMenus();
        const first = Array.prototype.find.call(submenus, m => remembered.indexOf(m.id) !== -1);
        if (first) {
            first.classList.add('show');
            const toggle = document.querySelector('#sidebar [href="#' + first.id + '"]');
            if (toggle) toggle.setAttribute('aria-expanded', 'true');
        }
    }

    // 2. Accordion — opening one submenu closes the others
    submenus.forEach(menu => {
        menu.addEventListener('show.bs.collapse', function () {
            submenus.forEach(other => {
                if (other !== menu && other.classList.contains('show')) {
                    const inst = bootstrap.Collapse.getInstance(other)
                              || new bootstrap.Collapse(other, { toggle: false });
                    inst.hide();
                }
            });
        });

        // 3. Persistence — record open/close
        menu.addEventListener('shown.bs.collapse', function () {
            const list = getOpenMenus();
            if (list.indexOf(menu.id) === -1) { list.push(menu.id); saveOpenMenus(list); }
        });
        menu.addEventListener('hidden.bs.collapse', function () {
            saveOpenMenus(getOpenMenus().filter(id => id !== menu.id));
        });
    });

    // 4. Scroll the active link into view (handy when the sidebar is tall).
    //
    // scrollIntoView() scrolls every scrollable ancestor, including the page —
    // so on a long editor, opening it could jump the main content down before
    // the user had touched anything. Scroll the sidebar's own scroll container
    // instead, leaving the window where it is. See spec item C5.
    const active = document.querySelector('#sidebar .nav-link.active');
    if (active && sidebar) {
        const linkBox = active.getBoundingClientRect();
        const navBox  = sidebar.getBoundingClientRect();
        const above   = linkBox.top < navBox.top;
        const below   = linkBox.bottom > navBox.bottom;
        if (above || below) {
            // Centre it within the sidebar, clamped to the scrollable range.
            const target = sidebar.scrollTop + (linkBox.top - navBox.top)
                         - (sidebar.clientHeight / 2) + (linkBox.height / 2);
            sidebar.scrollTop = Math.max(0, Math.min(target, sidebar.scrollHeight - sidebar.clientHeight));
        }
    }
});