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
            '}';
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

    function openFor(input) {
        if (!input || !input.files || !input.files[0]) return;
        currentInput = input;
        currentW = parseInt(input.dataset.cropW || '0', 10);
        currentH = parseInt(input.dataset.cropH || '0', 10);
        var label = input.dataset.cropLabel || 'Image';
        titleEl.textContent = 'Crop: ' + label;
        sizeEl.textContent  = currentW + ' x ' + currentH + ' px';

        // Locate the sibling hidden field that will hold the base64 result.
        // Convention: it has the same name as the file input + '_cropped'
        // and lives in the same form.
        var hiddenName = (input.name || '').replace(/_file$/, '') + '_cropped';
        currentTarget  = input.form ? input.form.querySelector('[name="' + hiddenName + '"]') : null;
        if (!currentTarget) {
            console.warn('cropper: no hidden input named "' + hiddenName + '" found in form');
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
                : '';
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
        reader.readAsDataURL(input.files[0]);
        // Reset the file input so the same file can be re-selected later.
        input.value = '';
    }

    function applyAndClose() {
        if (!cropper) return closeOverlay();
        var canvas = cropper.getCroppedCanvas(
            currentW > 0 && currentH > 0
                ? { width: currentW, height: currentH }
                : {}
        );
        if (!canvas) return closeOverlay();
        var dataUrl = canvas.toDataURL('image/jpeg', 0.92);
        if (currentTarget) {
            currentTarget.value = dataUrl;
            // Surface a preview if there's a sibling .crop-preview image
            if (currentInput) {
                var form = currentInput.form;
                var name = (currentInput.name || '').replace(/_file$/, '') + '_preview';
                var prev = form && form.querySelector('[data-crop-preview="' + name + '"]');
                if (prev) prev.src = dataUrl;
            }
            // Trigger change so any UI bindings update
            currentTarget.dispatchEvent(new Event('change', { bubbles: true }));
        }
        closeOverlay();
    }

    function closeOverlay() {
        overlay.classList.remove('show');
        if (cropper) { cropper.destroy(); cropper = null; }
        currentInput = null;
        currentTarget = null;
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
        if (t && t.matches && t.matches('input[type="file"].crop-input')) {
            openFor(t);
        }
    });
})();
