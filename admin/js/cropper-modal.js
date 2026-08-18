// admin/js/cropper-modal.js
// Reusable Cropper.js modal for any admin file input.
//
// Usage in any admin form:
//   <input type="file" accept="image/*" class="crop-input"
//          data-crop-w="1000" data-crop-h="1000" data-crop-label="Photo">
//   <input type="hidden" name="member_photo_cropped">  // sibling hidden field
//
// The hidden input name is auto-derived from the file input by appending
// "_cropped" to its own name. Cropper.js + its CSS are already loaded
// globally from admin/includes/header.php.

(function () {
    'use strict';

    if (typeof Cropper === 'undefined') {
        console.warn('Cropper.js not available; cropper modal disabled.');
        return;
    }

    function init() {
        // Build the overlay once
        var existing = document.getElementById('cropperOverlay');
        if (existing) return existing;

        var overlay = document.createElement('div');
        overlay.id = 'cropperOverlay';
        overlay.innerHTML =
            '<div id="cropperHeader">' +
                '<h4 class="m-0" id="cropperTitle">Crop Image</h4>' +
                '<button type="button" class="btn btn-sm btn-light" id="cropperCloseBtn">✕ Close</button>' +
            '</div>' +
            '<div id="cropperBody">' +
                '<div id="cropperHint">' +
                    'Drag the corners or move the box. Only the area inside the bright box will be saved — everything outside it will be discarded.' +
                '</div>' +
                '<div id="cropperMain">' +
                    '<div id="cropperImgWrap"><img id="cropperImg" alt=""></div>' +
                    '<aside id="cropperSidebar">' +
                        '<div class="cropper-side-label">Preview of what will be saved</div>' +
                        '<div id="cropperPreview"></div>' +
                        '<div id="cropperPreviewMeta" class="cropper-side-meta"></div>' +
                    '</aside>' +
                '</div>' +
            '</div>' +
            '<div id="cropperFooter">' +
                '<div class="btn-group">' +
                    '<button type="button" class="btn btn-outline-light" id="cropperRotateL"><i class="fas fa-undo"></i></button>' +
                    '<button type="button" class="btn btn-outline-light" id="cropperRotateR"><i class="fas fa-redo"></i></button>' +
                    '<button type="button" class="btn btn-outline-light" id="cropperReset">Reset</button>' +
                '</div>' +
                '<span id="cropperSize" class="text-light"></span>' +
                '<button type="button" class="btn btn-primary px-5 fw-bold" id="cropperApply">Apply Selection</button>' +
            '</div>';
        document.body.appendChild(overlay);

        var styles = document.createElement('style');
        styles.textContent =
            '#cropperOverlay { position: fixed; inset: 0; background: rgba(0,0,0,0.9); z-index: 10000; display: none; flex-direction: column; }' +
            '#cropperOverlay.show { display: flex; }' +
            '#cropperHeader { padding: 14px 24px; background: #222; color: #fff; display: flex; justify-content: space-between; align-items: center; }' +
            '#cropperBody { flex: 1; padding: 16px 24px; background: #111; display: flex; flex-direction: column; gap: 12px; min-height: 0; }' +
            '#cropperHint { color: #ffd34d; font-size: 14px; padding: 8px 12px; background: rgba(255,211,77,0.08); border-left: 3px solid #ffd34d; }' +
            '#cropperMain { flex: 1; display: flex; gap: 20px; min-height: 0; }' +
            '#cropperImgWrap { flex: 1; min-width: 0; max-height: 100%; display: flex; align-items: center; justify-content: center; }' +
            '#cropperImg { display: block; max-width: 100%; max-height: 100%; }' +
            '#cropperSidebar { width: 280px; flex-shrink: 0; color: #fff; display: flex; flex-direction: column; gap: 8px; }' +
            '.cropper-side-label { font-size: 12px; text-transform: uppercase; letter-spacing: 1px; opacity: 0.75; }' +
            '#cropperPreview { width: 100%; aspect-ratio: 1; overflow: hidden; background: #000; border: 1px solid #444; }' +
            '.cropper-side-meta { font-size: 12px; opacity: 0.7; line-height: 1.5; }' +
            '#cropperFooter { padding: 16px 24px; background: #222; display: flex; gap: 16px; justify-content: space-between; align-items: center; flex-wrap: wrap; }' +
            '@media (max-width: 767.98px) {' +
                '#cropperMain { flex-direction: column; }' +
                '#cropperSidebar { width: 100%; }' +
                '#cropperPreview { width: 200px; }' +
            '}' +
            /* Pending-upload strip shown under a `multiple` file input */
            '.cropper-pending-strip { margin-top: 10px; padding: 10px; border: 1px dashed #adb5bd; border-radius: 6px; background: rgba(13,110,253,0.04); }' +
            '.cropper-pending-caption { font-size: 12px; color: #6c757d; margin-bottom: 8px; }' +
            '.cropper-pending-row { display: flex; flex-wrap: wrap; gap: 8px; }' +
            '.cropper-pending-item { position: relative; width: 110px; height: 80px; }' +
            '.cropper-pending-item img { width: 100%; height: 100%; object-fit: cover; border: 1px solid #ced4da; border-radius: 4px; background: #fff; }' +
            '.cropper-pending-remove { position: absolute; top: 3px; right: 3px; width: 22px; height: 22px; line-height: 1; padding: 0;' +
                ' border: none; border-radius: 50%; background: rgba(220,53,69,0.92); color: #fff; font-size: 15px; cursor: pointer; }' +
            '.cropper-pending-remove:hover { background: #b02a37; }';
        document.head.appendChild(styles);

        return overlay;
    }

    var overlay   = init();
    if (!overlay) return;
    var imgEl       = document.getElementById('cropperImg');
    var titleEl     = document.getElementById('cropperTitle');
    var sizeEl      = document.getElementById('cropperSize');
    var closeBtn    = document.getElementById('cropperCloseBtn');
    var applyBtn    = document.getElementById('cropperApply');
    var rotateL     = document.getElementById('cropperRotateL');
    var rotateR     = document.getElementById('cropperRotateR');
    var resetBtn    = document.getElementById('cropperReset');
    var previewEl   = document.getElementById('cropperPreview');
    var previewMeta = document.getElementById('cropperPreviewMeta');

    var cropper       = null;
    var currentInput  = null;
    var currentTarget = null;
    var currentW      = 0;
    var currentH      = 0;
    var currentMime   = 'image/jpeg';
    var fileQueue     = [];   // remaining files when a `multiple` input is cropped one-by-one
    var queueTotal    = 0;

    function openFor(input) {
        if (!input || !input.files || !input.files[0]) return;
        // Snapshot the FileList before clearing the input — File objects stay
        // valid after input.value = ''.
        var files = Array.prototype.slice.call(input.files);
        // Reset the file input so the same file can be re-selected later.
        input.value = '';
        fileQueue  = input.multiple ? files : files.slice(0, 1);
        queueTotal = fileQueue.length;
        openNextFile(input);
    }

    function openNextFile(input) {
        var file = fileQueue.shift();
        if (!file) { closeOverlay(); return; }
        currentInput = input;
        currentMime  = file.type || 'image/jpeg';
        currentW = parseInt(input.dataset.cropW || '0', 10);
        currentH = parseInt(input.dataset.cropH || '0', 10);
        var label = input.dataset.cropLabel || 'Image';
        if (queueTotal > 1) {
            label += ' (' + (queueTotal - fileQueue.length) + ' of ' + queueTotal + ')';
        }
        titleEl.textContent = 'Crop: ' + label;
        sizeEl.textContent  = (currentW > 0 && currentH > 0)
            ? (currentW + ' x ' + currentH + ' px')
            : 'Free crop — original size kept';

        // Locate the sibling hidden field that will hold the base64 result.
        // Convention: it has the same name as the file input + '_cropped'
        // and lives in the same form. For `multiple` inputs (name="foo[]")
        // a hidden input foo_cropped[] is appended per applied crop instead.
        var baseName   = (input.name || '').replace(/\[\]$/, '').replace(/_file$/, '');
        if (input.multiple) {
            currentTarget = null; // created on apply
        } else {
            var hiddenName = baseName + '_cropped';
            currentTarget  = input.form ? input.form.querySelector('[name="' + hiddenName + '"]') : null;
            if (!currentTarget) {
                console.warn('cropper: no hidden input named "' + hiddenName + '" found in form');
            }
        }

        // Match the live preview's aspect to the target crop so the user
        // sees exactly the area that will be saved.
        if (previewEl) {
            previewEl.style.aspectRatio = (currentW > 0 && currentH > 0)
                ? (currentW + ' / ' + currentH) : '1 / 1';
        }
        if (previewMeta) {
            previewMeta.textContent = (currentW > 0 && currentH > 0)
                ? ('Output size: ' + currentW + ' × ' + currentH + ' px')
                : 'Free crop — saved at the selected size.';
        }

        var reader = new FileReader();
        reader.onload = function (e) {
            if (cropper) { cropper.destroy(); cropper = null; }
            imgEl.onload = function () {
                overlay.classList.add('show');
                cropper = new Cropper(imgEl, {
                    aspectRatio: currentH > 0 ? currentW / currentH : NaN,
                    viewMode: 1,
                    dragMode: 'crop',
                    autoCropArea: 1,
                    responsive: true,
                    background: true,
                    preview: '#cropperPreview',
                    ready: function () { this.cropper.crop(); }
                });
            };
            imgEl.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }

    /**
     * Show a thumbnail for each crop applied from a `multiple` input, with a
     * remove button that discards both the thumbnail and its hidden field — so
     * a mis-picked photo can be dropped before saving rather than after.
     * The strip is created lazily, directly after the file input.
     */
    function addPendingThumb(input, hiddenField, dataUrl) {
        var stripId = 'pending-' + (input.name || 'files').replace(/[^a-z0-9]/gi, '-');
        var strip = document.getElementById(stripId);
        if (!strip) {
            strip = document.createElement('div');
            strip.id = stripId;
            strip.className = 'cropper-pending-strip';
            var caption = document.createElement('div');
            caption.className = 'cropper-pending-caption';
            caption.textContent = 'Ready to upload — these are saved when you submit the form:';
            strip.appendChild(caption);
            var row = document.createElement('div');
            row.className = 'cropper-pending-row';
            strip.appendChild(row);
            input.parentNode.insertBefore(strip, input.nextSibling);
        }
        var row = strip.querySelector('.cropper-pending-row');

        var item = document.createElement('div');
        item.className = 'cropper-pending-item';

        var img = document.createElement('img');
        img.src = dataUrl;
        img.alt = '';

        var del = document.createElement('button');
        del.type = 'button';
        del.className = 'cropper-pending-remove';
        del.title = 'Remove this image';
        del.innerHTML = '&times;';
        del.addEventListener('click', function () {
            if (hiddenField.parentNode) hiddenField.parentNode.removeChild(hiddenField);
            item.parentNode.removeChild(item);
            if (!row.querySelector('.cropper-pending-item')) strip.remove();
        });

        item.appendChild(img);
        item.appendChild(del);
        row.appendChild(item);
    }

    function applyAndClose() {
        if (!cropper) return closeOverlay();
        // Preserve transparency: PNG/WebP/GIF sources export as PNG; photos
        // (JPEG) keep JPEG with a white fill behind any rotation gaps.
        var keepAlpha = /image\/(png|webp|gif)/.test(currentMime);
        var opts = currentW > 0 && currentH > 0
            ? { width: currentW, height: currentH }
            : {};
        if (!keepAlpha) opts.fillColor = '#fff';
        var canvas = cropper.getCroppedCanvas(opts);
        if (!canvas) return closeOverlay();
        var dataUrl = keepAlpha
            ? canvas.toDataURL('image/png')
            : canvas.toDataURL('image/jpeg', 0.92);

        var input    = currentInput;
        var form     = input ? input.form : null;
        var baseName = input ? (input.name || '').replace(/\[\]$/, '').replace(/_file$/, '') : '';

        if (input && input.multiple) {
            // Append one hidden field per applied crop: <base>_cropped[]
            if (form) {
                var hidden = document.createElement('input');
                hidden.type  = 'hidden';
                hidden.name  = baseName + '_cropped[]';
                hidden.value = dataUrl;
                input.parentNode.insertBefore(hidden, input);
                // ...and show it. A `multiple` input previously produced no
                // visible feedback at all: an editor cropping four event photos
                // saw nothing until after saving, with no way to tell how many
                // had been accepted or to drop one they'd picked by mistake.
                // See docs/superpowers/specs/2026-08-18-cms-batch-c-design.md (C3).
                addPendingThumb(input, hidden, dataUrl);
            }
        } else if (currentTarget) {
            currentTarget.value = dataUrl;
            // Surface a preview if there's a sibling .crop-preview image
            if (form) {
                var prev = form.querySelector('[data-crop-preview="' + baseName + '_preview"]');
                if (prev) prev.src = dataUrl;
            }
            // Trigger change so any UI bindings update
            currentTarget.dispatchEvent(new Event('change', { bubbles: true }));
        }

        // More files queued from a `multiple` input? Keep the overlay open
        // and move straight to the next one.
        if (fileQueue.length > 0 && input) {
            if (cropper) { cropper.destroy(); cropper = null; }
            openNextFile(input);
            return;
        }
        closeOverlay();
    }

    function closeOverlay() {
        overlay.classList.remove('show');
        if (cropper) { cropper.destroy(); cropper = null; }
        currentInput = null;
        currentTarget = null;
        fileQueue = [];
        queueTotal = 0;
    }

    // Wire up controls
    closeBtn.addEventListener('click', closeOverlay);
    applyBtn.addEventListener('click', applyAndClose);
    rotateL .addEventListener('click', function () { if (cropper) cropper.rotate(-90); });
    rotateR .addEventListener('click', function () { if (cropper) cropper.rotate(90); });
    resetBtn.addEventListener('click', function () { if (cropper) cropper.reset(); });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && overlay.classList.contains('show')) closeOverlay();
    });

    // Delegate the file-input change so any `.crop-input` in any
    // dynamically-rendered admin section is handled.
    document.addEventListener('change', function (e) {
        var t = e.target;
        if (!(t && t.matches && t.matches('input[type="file"].crop-input'))) return;
        if (!t.files || !t.files.length) return;
        // SVGs stay vector and non-images (e.g. PDFs) aren't croppable —
        // leave the input untouched so the normal upload path handles them.
        // For multi-selects, every file must be croppable or none is intercepted.
        var croppable = function (f) {
            return /^image\//.test(f.type) && f.type !== 'image/svg+xml';
        };
        if (!Array.prototype.every.call(t.files, croppable)) return;
        openFor(t);
    });
})();
