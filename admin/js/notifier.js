/**
 * admin/js/notifier.js — the notification bell in the admin's top bar.
 *
 * While any admin page is open it asks admin/api/notifications.php about
 * once a minute what is waiting, and:
 *   - shows the number of new, unopened submissions on the bell and in the
 *     tab title, e.g. "(3) Contact Us — ESWASA Admin";
 *   - lists the newest ones in the bell's menu, each linking straight to it;
 *   - pops up a notice in the corner when something new arrives, and a
 *     desktop notification too if this browser has been allowed to show
 *     them and the admin is in another tab or window;
 *   - keeps the sidebar and page counters in step (through inbox.js).
 *
 * "New" means not yet seen by this browser. The keys of submissions already
 * announced are kept in localStorage, so reloading a page or signing in the
 * next morning announces only what actually arrived in between.
 */
(function () {
    'use strict';

    var ENDPOINT    = 'api/notifications.php';
    var INTERVAL_MS = 60000;
    var SEEN_KEY    = 'eswasa_seen_submissions';
    var DESKTOP_KEY = 'eswasa_desktop_alerts';
    var MAX_SEEN    = 300;

    var root = document.getElementById('notifier');
    if (!root || !window.fetch) {
        return;
    }
    var totalEl   = root.querySelector('[data-notifier-total]');
    var listEl    = root.querySelector('[data-notifier-list]');
    var statusEl  = root.querySelector('[data-notifier-status]');
    var desktopEl = root.querySelector('[data-notifier-desktop]');
    var toggleEl  = document.getElementById('notifierToggle');
    var baseTitle = document.title.replace(/^\(\d+\)\s*/, '');
    var timer = null;
    var inFlight = false;
    var stopped = false;

    // ── storage (may be unavailable, e.g. private windows) ──────────────
    function load(key, fallback) {
        try {
            var v = window.localStorage.getItem(key);
            return v === null ? fallback : JSON.parse(v);
        } catch (e) {
            return fallback;
        }
    }
    function save(key, value) {
        try {
            window.localStorage.setItem(key, JSON.stringify(value));
        } catch (e) { /* not remembered; harmless */ }
    }

    // ── rendering ───────────────────────────────────────────────────────
    function ago(seconds) {
        if (seconds < 60) return 'just now';
        var m = Math.floor(seconds / 60);
        if (m < 60) return m + ' min ago';
        var h = Math.floor(m / 60);
        if (h < 24) return h + (h === 1 ? ' hour ago' : ' hours ago');
        var d = Math.floor(h / 24);
        return d + (d === 1 ? ' day ago' : ' days ago');
    }

    function el(tag, className, text) {
        var node = document.createElement(tag);
        if (className) node.className = className;
        if (text !== undefined) node.textContent = text;
        return node;
    }

    function renderList(items, total) {
        listEl.replaceChildren();
        if (!items.length) {
            listEl.appendChild(el('div', 'px-3 py-4 text-center text-muted small', 'Nothing new. Everything has been opened.'));
            return;
        }
        items.forEach(function (item) {
            var a = el('a', 'notifier-item');
            a.href = item.url;
            var icon = el('i', 'fas ' + item.icon + ' notifier-icon');
            var body = el('div', 'notifier-body');
            var head = el('div', 'd-flex justify-content-between gap-2');
            head.appendChild(el('strong', 'text-truncate', item.label));
            head.appendChild(el('small', 'text-muted text-nowrap', ago(item.age)));
            body.appendChild(head);
            body.appendChild(el('div', 'text-truncate', item.who));
            if (item.what) {
                body.appendChild(el('div', 'notifier-what text-truncate', item.what));
            }
            a.appendChild(icon);
            a.appendChild(body);
            listEl.appendChild(a);
        });
        if (total > items.length) {
            listEl.appendChild(el('div', 'px-3 py-2 small text-muted text-center',
                'and ' + (total - items.length) + ' more in the inboxes'));
        }
    }

    function renderTotal(total) {
        totalEl.textContent = total > 99 ? '99+' : String(total);
        totalEl.classList.toggle('d-none', total === 0);
        toggleEl.setAttribute('aria-label', 'New submissions: ' + total);
        document.title = (total > 0 ? '(' + total + ') ' : '') + baseTitle;
    }

    // ── announcing new arrivals ─────────────────────────────────────────
    function toastArea() {
        var area = document.getElementById('notifierToasts');
        if (!area) {
            area = el('div', 'toast-container position-fixed bottom-0 end-0 p-3');
            area.id = 'notifierToasts';
            document.body.appendChild(area);
        }
        return area;
    }

    function toast(title, text, url) {
        if (!window.bootstrap || !bootstrap.Toast) return;
        var t = el('div', 'toast');
        t.setAttribute('role', 'status');
        t.setAttribute('aria-live', 'polite');
        var header = el('div', 'toast-header');
        header.appendChild(el('i', 'fas fa-bell me-2 text-primary'));
        header.appendChild(el('strong', 'me-auto', title));
        var close = el('button', 'btn-close');
        close.type = 'button';
        close.setAttribute('data-bs-dismiss', 'toast');
        close.setAttribute('aria-label', 'Close');
        header.appendChild(close);
        var body = el('div', 'toast-body');
        body.appendChild(document.createTextNode(text + ' '));
        if (url) {
            var link = el('a', 'fw-semibold', 'Open');
            link.href = url;
            body.appendChild(link);
        }
        t.appendChild(header);
        t.appendChild(body);
        toastArea().appendChild(t);
        t.addEventListener('hidden.bs.toast', function () { t.remove(); });
        new bootstrap.Toast(t, { delay: 12000 }).show();
    }

    function desktopAllowed() {
        return 'Notification' in window && Notification.permission === 'granted' && load(DESKTOP_KEY, false) === true;
    }

    function desktop(title, text, url) {
        if (!desktopAllowed() || !document.hidden) return;
        try {
            var n = new Notification(title, { body: text, icon: '../assets/img/favicon.png', tag: 'eswasa-' + title });
            n.onclick = function () {
                window.focus();
                if (url) window.location.href = url;
                n.close();
            };
        } catch (e) { /* some browsers only allow this from a service worker */ }
    }

    function announce(fresh, total) {
        if (!fresh.length) return;
        if (fresh.length <= 3) {
            fresh.forEach(function (item) {
                var text = item.who + (item.what ? ' — ' + item.what : '');
                toast('New ' + item.label.toLowerCase(), text, item.url);
                desktop('New ' + item.label.toLowerCase(), text, item.url);
            });
        } else {
            var text = fresh.length + ' new submissions have arrived.';
            toast('New submissions', text, null);
            desktop('ESWASA: ' + fresh.length + ' new submissions', text, null);
        }
    }

    // ── polling ─────────────────────────────────────────────────────────
    function schedule(ms) {
        clearTimeout(timer);
        if (!stopped) {
            timer = setTimeout(poll, ms);
        }
    }

    function poll() {
        if (inFlight || stopped) return;
        inFlight = true;
        fetch(ENDPOINT, {
            credentials: 'same-origin',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            cache: 'no-store'
        })
            .then(function (r) {
                if (r.status === 401) {
                    stopped = true;
                    statusEl.textContent = 'You have been signed out. Reload the page to sign in again.';
                    return null;
                }
                return r.ok ? r.json() : Promise.reject(r.status);
            })
            .then(function (data) {
                if (!data) return;
                renderTotal(data.total);
                renderList(data.items, data.total);
                statusEl.textContent = 'Checked at ' + new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) + '. Checks every minute.';
                document.dispatchEvent(new CustomEvent('eswasa:counts', { detail: data.counts }));

                var seen = load(SEEN_KEY, null);
                var firstRun = seen === null;
                seen = seen || [];
                var fresh = data.items.filter(function (item) { return seen.indexOf(item.key) === -1; });
                if (!firstRun) {
                    announce(fresh, data.total);
                }
                // The very first time, just remember what is already there
                // rather than announcing a backlog as if it had just arrived.
                save(SEEN_KEY, seen.concat(fresh.map(function (i) { return i.key; })).slice(-MAX_SEEN));
            })
            .catch(function () {
                statusEl.textContent = 'Could not check for new submissions just now. Trying again shortly.';
            })
            .then(function () {
                inFlight = false;
                schedule(INTERVAL_MS);
            });
    }

    // ── desktop alerts switch ───────────────────────────────────────────
    function renderDesktopButton() {
        if (!('Notification' in window) || !desktopEl) return;
        desktopEl.classList.remove('d-none');
        if (Notification.permission === 'denied') {
            desktopEl.textContent = 'Desktop alerts: blocked';
            desktopEl.title = 'Allow notifications for this site in the browser settings to turn them on.';
            desktopEl.disabled = true;
            return;
        }
        desktopEl.textContent = desktopAllowed() ? 'Desktop alerts: on' : 'Desktop alerts: off';
        desktopEl.title = 'Show a desktop notification when something arrives while you are in another tab.';
    }

    if (desktopEl) {
        desktopEl.addEventListener('click', function () {
            if (desktopAllowed()) {
                save(DESKTOP_KEY, false);
                renderDesktopButton();
                return;
            }
            Notification.requestPermission().then(function (p) {
                save(DESKTOP_KEY, p === 'granted');
                renderDesktopButton();
            });
        });
        renderDesktopButton();
    }

    // Check straight away, then every minute; also when the menu is opened,
    // when the tab comes back into view, and right after something is
    // opened (inbox.js), so the list never shows what was just read.
    root.addEventListener('show.bs.dropdown', function () { poll(); });
    document.addEventListener('visibilitychange', function () {
        if (!document.hidden) poll();
    });
    document.addEventListener('eswasa:viewed', function (e) {
        var counts = e.detail || {};
        renderTotal(Object.keys(counts).reduce(function (s, k) { return s + (parseInt(counts[k], 10) || 0); }, 0));
        schedule(300);
    });

    renderTotal(parseInt(totalEl.textContent, 10) || 0);
    poll();
})();
