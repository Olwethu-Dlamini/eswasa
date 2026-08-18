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
/* ══════════════════════════════════════════════
   EDITOR CHROME
   Gives every content editor the same shape without rewriting any form.

   Two problems this solves. Editors had grown three different layouts —
   tabbed, jump-nav, and a plain scroll — and the plain ones got long:
   services_edit is sixteen cards deep with no navigation and the Save button
   only at the very bottom. So: build a section rail from the headings each
   page already has, and keep Save permanently in reach.

   Both are additive. Nothing is moved, hidden, or re-parented, so no form
   loses an input and no page's save handler sees a different POST.
════════════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', function () {
    var main = document.getElementById('mainContent');
    if (!main) return;

    /* ── Section rail ────────────────────────────
       Only worth showing when there is enough to navigate; on a two-section
       page it is noise. Tabbed editors are skipped — they already chunk their
       content, and a rail pointing into hidden panes would scroll to nothing. */
    var isTabbed = !!main.querySelector('[data-bs-toggle="tab"]');
    var headings = Array.prototype.filter.call(
        main.querySelectorAll('.card .card-body > h5'),
        function (h) { return h.textContent.trim() !== ''; }
    );

    if (!isTabbed && headings.length >= 3) {
        var rail = document.createElement('nav');
        rail.className = 'editor-rail';
        rail.setAttribute('aria-label', 'Sections on this page');

        var label = document.createElement('span');
        label.className = 'editor-rail__label';
        label.textContent = 'Jump to';
        rail.appendChild(label);

        headings.forEach(function (h, i) {
            if (!h.id) h.id = 'sec-auto-' + i;
            var a = document.createElement('a');
            a.href = '#' + h.id;
            // Headings can carry an accent tick and entities; use the text only.
            var text = h.textContent.replace(/\s+/g, ' ').trim();
            a.textContent = text.length > 34 ? text.slice(0, 33).trimEnd() + '…' : text;
            if (text.length > 34) a.title = text;
            rail.appendChild(a);
        });

        // Sits directly under the page header so it is the first thing after
        // the title, not floating in the middle of the form.
        var header = main.querySelector('.d-flex.border-bottom');
        if (header && header.parentNode) {
            header.parentNode.insertBefore(rail, header.nextSibling);
        } else {
            main.insertBefore(rail, main.firstChild);
        }

        // Mark whichever section is currently in view.
        if ('IntersectionObserver' in window) {
            var links = {};
            headings.forEach(function (h) {
                links[h.id] = rail.querySelector('a[href="#' + h.id + '"]');
            });
            var seen = new Set();
            var io = new IntersectionObserver(function (entries) {
                entries.forEach(function (e) {
                    if (e.isIntersecting) seen.add(e.target.id); else seen.delete(e.target.id);
                });
                headings.forEach(function (h) {
                    if (links[h.id]) links[h.id].classList.remove('is-current');
                });
                for (var i = 0; i < headings.length; i++) {
                    if (seen.has(headings[i].id)) {
                        if (links[headings[i].id]) links[headings[i].id].classList.add('is-current');
                        break;
                    }
                }
            }, { rootMargin: '-150px 0px -60% 0px' });
            headings.forEach(function (h) { io.observe(h); });
        }
    }

    /* ── Sticky save bar ─────────────────────────
       The primary submit is often at the bottom of a very long form. Rather
       than move it — which would change which form it belongs to — a fixed bar
       proxies a click through to the real button, so the page's own handler
       runs exactly as before.

       Buttons inside a modal are skipped: those are "Add ..." actions for a
       dialog, not the page's save. */
    var candidates = Array.prototype.filter.call(
        main.querySelectorAll('button[type="submit"].btn-primary, input[type="submit"].btn-primary'),
        function (b) {
            if (b.closest('.modal')) return false;
            // Only a POST form saves anything. The Activity Log's only primary
            // button submits a GET filter, and a sticky bar telling someone
            // their changes are unsaved would be nonsense on a read-only page.
            var form = b.form || b.closest('form');
            return !!form && (form.method || 'get').toLowerCase() === 'post';
        }
    );

    // Only promote a save when there is exactly one, and the page hasn't
    // already got its own sticky save. Several editors carry more than one
    // primary submit belonging to different forms — Publications has a page
    // save and a per-folder save — and a bar wired to the wrong one would
    // submit the wrong form. Where it is ambiguous, leave the page alone.
    var alreadySticky = !!main.querySelector('.save-pill, .editor-savebar');
    var realSave = (candidates.length === 1 && !alreadySticky) ? candidates[0] : null;

    if (realSave) {
        var bar = document.createElement('div');
        bar.className = 'editor-savebar';

        var hint = document.createElement('span');
        hint.className = 'editor-savebar__hint';
        hint.textContent = 'Changes are not saved until you use this button.';
        bar.appendChild(hint);

        var proxy = document.createElement('button');
        proxy.type = 'button';
        proxy.className = 'btn btn-primary';
        proxy.innerHTML = realSave.innerHTML;
        proxy.addEventListener('click', function () { realSave.click(); });
        bar.appendChild(proxy);

        main.appendChild(bar);

        // With a bar always visible, the original at the foot of the page is a
        // duplicate. Hide it rather than remove it: the form still owns it, so
        // the proxy's click submits exactly what it always did.
        var originalRow = realSave.parentNode;
        if (originalRow && originalRow !== main) {
            realSave.style.visibility = 'hidden';
            realSave.style.position = 'absolute';
            realSave.style.pointerEvents = 'none';
        }
    }
});
