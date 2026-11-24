{{-- Fit 540px cards to their box, and download a card as a 1080 x 1080 PNG. --}}
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

    window.celDownload = function (card, btn) {
        var label = btn ? btn.innerHTML : '';
        if (btn) { btn.disabled = true; btn.innerHTML = 'Preparing…'; }
        var done = function () { if (btn) { btn.disabled = false; btn.innerHTML = label; } };
        var fonts = (document.fonts && document.fonts.ready) ? document.fonts.ready : Promise.resolve();
        Promise.all([loadLib(), fonts]).then(function () {
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
            return Promise.all(waits).then(function () { return html2canvas(copy, { scale: 2, useCORS: true, backgroundColor: null, logging: false }).then(function (canvas) {
                document.body.removeChild(holder);
                var a = document.createElement('a');
                a.download = (card.getAttribute('data-file') || 'celebration') + '.png';
                a.href = canvas.toDataURL('image/png');
                document.body.appendChild(a); a.click(); a.remove();
                done();
            }); });
        }).catch(function () {
            done();
            var msg = document.createElement('div');
            msg.className = 'alert alert-warning mt-2';
            msg.textContent = 'Could not make the image. Check the internet connection and try again.';
            (btn ? btn.parentNode : document.body).appendChild(msg);
        });
    };
})();
</script>
@endonce
