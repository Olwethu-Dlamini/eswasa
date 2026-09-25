/**
 * admin/js/inbox.js — opening a submission marks it as viewed.
 *
 * Every inbox (contact messages, customer feedback, the quote requests and
 * training applications) used to show a submission as New until someone
 * changed its status by hand, so "New" said nothing about whether anyone had
 * actually read it. Now, the moment one is opened, it is marked as viewed on
 * the server (admin/api/mark_viewed.php), who opened it and when is
 * recorded, and the page updates in place.
 *
 * Markup an inbox page provides — "key:id" is e.g. "contact:12":
 *
 *   <tr data-inbox="contact" data-inbox-id="12" data-unread="1"
 *       data-viewed-status="read" data-viewed-label="Read"
 *       data-viewed-class="bg-success">
 *     … <button data-inbox-open>View</button> …
 *   </tr>
 *
 *   [data-inbox-status="key:id"]         badges relabelled to the viewed label
 *   [data-inbox-status-select="key:id"]  selects set to the viewed status
 *   [data-inbox-unread-only="key:id"]    hidden once viewed
 *   [data-inbox-read-only="key:id"]      shown once viewed
 *   [data-inbox-viewed-by="key:id"]      filled with "Opened by … on …"
 *   [data-inbox-count="key"]             any unread counter for that inbox
 *                                        (sidebar badges, tab badges)
 *
 * A link ending in &view=<id> (as in the notification emails) opens that
 * submission as soon as the page loads.
 */
(function () {
    'use strict';

    var ENDPOINT = 'api/mark_viewed.php';

    function each(selector, fn) {
        Array.prototype.forEach.call(document.querySelectorAll(selector), fn);
    }

    function applyViewed(row, reply) {
        var ref = row.dataset.inbox + ':' + row.dataset.inboxId;
        var q = function (attr) { return '[' + attr + '="' + ref + '"]'; };

        row.classList.remove('inbox-unread');
        row.dataset.unread = '0';

        if (row.dataset.viewedLabel) {
            each(q('data-inbox-status'), function (el) {
                el.className = 'badge ' + (row.dataset.viewedClass || 'bg-secondary');
                el.textContent = row.dataset.viewedLabel;
            });
        }
        if (row.dataset.viewedStatus) {
            each(q('data-inbox-status-select'), function (el) { el.value = row.dataset.viewedStatus; });
        }
        each(q('data-inbox-unread-only'), function (el) { el.classList.add('d-none'); });
        each(q('data-inbox-read-only'), function (el) { el.classList.remove('d-none'); });
        if (reply && reply.changed) {
            each(q('data-inbox-viewed-by'), function (el) {
                el.textContent = 'Opened by ' + reply.viewed_by + ' on ' + reply.viewed_at + '.';
                el.classList.remove('d-none');
            });
        }
    }

    function updateCounts(counts) {
        Object.keys(counts || {}).forEach(function (key) {
            var n = parseInt(counts[key], 10) || 0;
            each('[data-inbox-count="' + key + '"]', function (el) {
                var suffix = el.dataset.countSuffix || '';
                el.textContent = n + suffix;
                el.classList.toggle('d-none', n === 0);
            });
        });
    }

    function markViewed(row) {
        if (row.dataset.unread !== '1' || row.dataset.pending === '1') {
            return;
        }
        row.dataset.pending = '1';
        fetch(ENDPOINT, {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: new URLSearchParams({ inbox: row.dataset.inbox, id: row.dataset.inboxId })
        })
            .then(function (r) { return r.ok ? r.json() : Promise.reject(r.status); })
            .then(function (reply) {
                applyViewed(row, reply);
                // Badges on this page and the notification bell listen for this.
                document.dispatchEvent(new CustomEvent('eswasa:counts', { detail: reply.counts }));
            })
            .catch(function () {
                // Left unread; the next open tries again.
            })
            .then(function () { row.dataset.pending = '0'; });
    }

    // Opening = clicking a row's View button. Delegated, so the pages keep
    // their own click handlers and nothing depends on load order.
    document.addEventListener('click', function (e) {
        var btn = e.target.closest && e.target.closest('[data-inbox-open]');
        if (!btn) {
            return;
        }
        var row = btn.closest('[data-inbox-id]');
        if (row) {
            markViewed(row);
        }
    });

    // Fresh counts, from here or from the notifier's polling (notifier.js):
    // keep every badge on the page in step.
    document.addEventListener('eswasa:counts', function (e) { updateCounts(e.detail); });

    // Deep link from a notification email: …&view=12 opens #12.
    document.addEventListener('DOMContentLoaded', function () {
        var params = new URLSearchParams(window.location.search);
        var id = params.get('view');
        if (!id) {
            return;
        }
        // Drop it from the address bar, so reloading after an action (every
        // form here redirects back to the same URL) doesn't reopen it.
        params.delete('view');
        var qs = params.toString();
        history.replaceState(null, '', window.location.pathname + (qs ? '?' + qs : '') + window.location.hash);

        var row = document.querySelector('[data-inbox-id="' + CSS.escape(id) + '"]');
        if (!row) {
            var main = document.getElementById('mainContent');
            if (main) {
                var note = document.createElement('div');
                note.className = 'alert alert-warning';
                note.textContent = 'That submission is no longer here. It may have been deleted.';
                main.insertBefore(note, main.firstChild);
            }
            return;
        }
        // An inbox inside a tab (Customer Feedback) must have that tab showing.
        var pane = row.closest('.tab-pane');
        if (pane && !pane.classList.contains('active') && window.bootstrap) {
            var tabBtn = document.querySelector('[data-bs-target="#' + pane.id + '"]');
            if (tabBtn) {
                bootstrap.Tab.getOrCreateInstance(tabBtn).show();
            }
        }
        row.scrollIntoView({ block: 'center' });
        row.classList.add('inbox-flash');
        var open = row.querySelector('[data-inbox-open]');
        if (open) {
            open.click();
        }
    });
})();
