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

    // 1. Restore persisted open state (in addition to the active-page one from PHP)
    const remembered = getOpenMenus();
    submenus.forEach(menu => {
        if (remembered.indexOf(menu.id) !== -1) {
            menu.classList.add('show');
            const toggle = document.querySelector('#sidebar [href="#' + menu.id + '"]');
            if (toggle) toggle.setAttribute('aria-expanded', 'true');
        }
    });

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

    // 4. Scroll the active link into view (handy when the sidebar is tall)
    const active = document.querySelector('#sidebar .nav-link.active');
    if (active) {
        const r = active.getBoundingClientRect();
        const visible = r.top >= 0 && r.bottom <= window.innerHeight;
        if (!visible) active.scrollIntoView({ block: 'center' });
    }
});