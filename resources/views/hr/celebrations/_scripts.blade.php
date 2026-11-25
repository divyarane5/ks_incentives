{{-- Fit 540px cards to their box, and download or share a card as a 1080 x 1080 PNG. --}}
@once
<script>
(function () {
    function fit() {
        document.querySelectorAll('.cel-fit').forEach(function (box) {
            var card = box.querySelector('.cel-card');
            if (!card) return;
            var s = box.clientWidth / 540;
            card.style.transform = 'scale(' + s + ')';
            box.style.height = (540 * s) + 'px';
        });
    }
    window.celFit = fit;
    window.addEventListener('resize', fit);
    document.addEventListener('DOMContentLoaded', fit);
    fit();

    var lib = null;
    function loadLib() {
        if (window.html2canvas) return Promise.resolve();
        if (lib) return lib;
        lib = new Promise(function (ok, bad) {
            var s = document.createElement('script');
            s.src = 'https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js';
            s.onload = ok; s.onerror = function () { lib = null; bad(); };
            document.head.appendChild(s);
        });
        return lib;
    }

    // Draw a card as a 1080 x 1080 canvas.
    function render(card) {
        var fonts = (document.fonts && document.fonts.ready) ? document.fonts.ready : Promise.resolve();
        return Promise.all([loadLib(), fonts]).then(function () {
            var holder = document.createElement('div');
            holder.style.cssText = 'position:fixed;left:-10000px;top:0;width:540px;height:540px;';
            var copy = card.cloneNode(true);
            copy.style.transform = 'none';
            holder.appendChild(copy);
            document.body.appendChild(holder);
            // Inline SVG drawings (waves, laurels, icons) become images first: the image library draws those reliably.
            var waits = Array.prototype.map.call(copy.querySelectorAll('svg'), function (svg) {
                var cs = getComputedStyle(svg);
                var clone = svg.cloneNode(true);
                clone.setAttribute('xmlns', 'http://www.w3.org/2000/svg');
                clone.removeAttribute('style');
                var img = new Image();
                img.style.cssText = svg.getAttribute('style') || '';
                img.style.position = cs.position === 'static' ? 'static' : 'absolute';
                img.style.display = cs.display === 'inline' ? 'inline-block' : cs.display;
                img.width = svg.width.baseVal.value; img.height = svg.height.baseVal.value;
                var p = new Promise(function (ok) { img.onload = ok; img.onerror = ok; });
                img.src = 'data:image/svg+xml;charset=utf-8,' + encodeURIComponent(new XMLSerializer().serializeToString(clone));
                svg.parentNode.replaceChild(img, svg);
                return p;
            });
            return Promise.all(waits).then(function () {
                return html2canvas(copy, { scale: 2, useCORS: true, backgroundColor: null, logging: false });
            }).then(function (canvas) { document.body.removeChild(holder); return canvas; },
                     function (e) { document.body.removeChild(holder); throw e; });
        });
    }

    function busy(btn, text) {
        var label = btn ? btn.innerHTML : '';
        if (btn) { btn.disabled = true; btn.innerHTML = text; }
        return function () { if (btn) { btn.disabled = false; btn.innerHTML = label; } };
    }
    function failed(btn) {
        var msg = document.createElement('div');
        msg.className = 'alert alert-warning mt-2';
        msg.textContent = 'Could not make the image. Check the internet connection and try again.';
        (btn ? btn.parentNode : document.body).appendChild(msg);
    }
    function save(canvas, card) {
        var a = document.createElement('a');
        a.download = (card.getAttribute('data-file') || 'celebration') + '.png';
        a.href = canvas.toDataURL('image/png');
        document.body.appendChild(a); a.click(); a.remove();
    }

    window.celDownload = function (card, btn) {
        var done = busy(btn, 'Preparing…');
        render(card).then(function (canvas) { save(canvas, card); done(); })
            .catch(function () { done(); failed(btn); });
    };

    /**
     * Share the card picture on WhatsApp (or anywhere). On phones this opens the share sheet with the
     * picture attached, so HR picks WhatsApp and the group. On a computer the picture is downloaded and
     * WhatsApp opens with the text, ready to attach it.
     */
    window.celShare = function (card, btn, text) {
        var done = busy(btn, 'Preparing…');
        text = text || '';
        render(card).then(function (canvas) {
            return new Promise(function (ok) { canvas.toBlob(ok, 'image/png'); }).then(function (blob) {
                var name = (card.getAttribute('data-file') || 'celebration') + '.png';
                var file = blob ? new File([blob], name, { type: 'image/png' }) : null;
                if (file && navigator.canShare && navigator.canShare({ files: [file] })) {
                    return navigator.share({ files: [file], text: text }).catch(function () {}).then(done);
                }
                save(canvas, card);
                window.open('https://wa.me/?text=' + encodeURIComponent(text), '_blank', 'noopener');
                done();
            });
        }).catch(function () { done(); failed(btn); });
    };
})();
</script>
@endonce
