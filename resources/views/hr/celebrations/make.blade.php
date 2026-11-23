@extends('layouts.app')

@section('content')
@include('hr.partials.hr-styles')
@php $c0 = $cards[$design]; @endphp
<style>
    .cel-layout { display: grid; gap: 14px; }
    .cel-step-h { display: flex; align-items: center; gap: 8px; margin-bottom: 10px; }
    .cel-step-h .n { width: 24px; height: 24px; border-radius: 50%; background: var(--kp-navy); color: #fff; font-size: .75rem; font-weight: 700; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .cel-step-h h2 { font-size: .95rem !important; margin: 0; color: var(--kp-navy); font-weight: 700; }
    .cel-box { background: #fff; border: 1px solid var(--kp-line); border-radius: 14px; padding: 14px; }
    .cel-sub { font-size: .78rem; font-weight: 600; color: var(--kp-muted); }
    .cel-preview-box { background: #eef1f6; border-radius: 14px; padding: 14px; }
    .cel-preview-box .cel-stage { max-width: 380px; margin: 0 auto; }
    .cel-preview-tools { display: flex; gap: 8px; justify-content: center; margin-top: 10px; }
    .cel-preview-tools .btn { min-height: 40px; border-radius: 10px; font-weight: 600; }
    .cel-pick { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; }
    @media (min-width: 576px) { .cel-pick { grid-template-columns: repeat(4, minmax(0, 1fr)); } }
    .cel-pick label { cursor: pointer; margin: 0; border: 2px solid var(--kp-line); border-radius: 12px; padding: 5px 5px 7px; background: #fff; text-align: center; font-size: .75rem; font-weight: 600; color: var(--kp-muted); min-width: 0; transition: border-color .15s; }
    .cel-pick input { display: none; }
    .cel-pick label:has(input:checked) { border-color: var(--kp-gold); color: var(--kp-navy); background: #fffdf3; }
    .cel-pick .cel-fit { border-radius: 8px; box-shadow: none; margin-bottom: 5px; pointer-events: none; }
    .cel-thumb { width: 48px; height: 48px; border-radius: 50%; background: #e8ecf4 center/cover no-repeat; flex-shrink: 0; display: flex; align-items: center; justify-content: center; font-weight: 700; color: var(--kp-navy); border: 2px solid var(--kp-gold); }
    .cel-big { display: none; }
    .cel-big.on { display: block; }
    .cel-actions { position: sticky; bottom: 8px; z-index: 10; background: #fff; border: 1px solid var(--kp-line); box-shadow: 0 6px 20px rgba(1, 30, 72, .12); padding: 8px; border-radius: 14px; display: flex; gap: 8px; }
    @media (max-width: 575px) { .cel-preview-box .cel-stage { max-width: 300px; } }
    .cel-actions .btn { min-height: 46px; border-radius: 10px; font-weight: 700; }
    .hr-form .form-control { font-size: 16px; }
    @media (min-width: 992px) {
        .cel-layout { grid-template-columns: minmax(0, 460px) minmax(0, 1fr); align-items: start; }
        .cel-left { position: sticky; top: 80px; }
        .cel-preview-box .cel-stage { max-width: 432px; }
        .cel-actions { position: static; border: 0; box-shadow: none; padding: 0; background: transparent; }
    }
</style>
<div class="container-xxl flex-grow-1 container-p-y kp">
    <div class="kp-crumb"><a href="{{ route('celebrations.index') }}">Celebrations</a> <i class="bx bx-chevron-right"></i> {{ $person->name }}</div>
    <div class="hr-head">
        <div>
            <h4>{{ $type === 'birthday' ? '🎂 Birthday post' : '🏆 Work anniversary post' }}</h4>
            <div class="sub">{{ $person->name }} · {{ $date->format('l, d M Y') }}@if($years) · {{ \App\Services\CelebrationService::yearsWord($years) }}@endif</div>
        </div>
    </div>

    <form method="POST" action="{{ route('celebrations.store') }}" class="hr-form" enctype="multipart/form-data" id="cel-form">
        @csrf
        <input type="hidden" name="user_id" value="{{ $person->id }}">
        <input type="hidden" name="type" value="{{ $type }}">
        <input type="hidden" name="years" value="{{ $years }}">

        <div class="cel-layout">
            {{-- Live preview --}}
            <div class="cel-left">
                <div class="cel-preview-box">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="cel-sub"><i class="bx bx-show"></i> Live preview</span>
                        <span class="cel-sub d-none d-sm-inline">Drag the photo to move it</span>
                    </div>
                    <div class="cel-stage">
                        @foreach($cards as $d => $c)
                            <div class="cel-big {{ $d === $design ? 'on' : '' }}" data-design="{{ $d }}">
                                <div class="cel-fit">@include('hr.celebrations._card', ['c' => $c])</div>
                            </div>
                        @endforeach
                    </div>
                    <div class="cel-preview-tools">
                        <button type="button" class="btn btn-outline-primary bg-white" id="cel-preview"><i class="bx bx-fullscreen"></i> Full preview</button>
                        <button type="button" class="btn btn-outline-primary bg-white" id="cel-dl"><i class="bx bx-download"></i> Download</button>
                    </div>
                </div>
            </div>

            {{-- Steps --}}
            <div class="cel-right d-grid gap-3">
                <div class="cel-box">
                    <div class="cel-step-h"><span class="n">1</span><h2>Choose a design</h2></div>
                    <div class="cel-pick">
                        @foreach($cards as $d => $c)
                            <label>
                                <input type="radio" name="design" value="{{ $d }}" @checked($d === $design)>
                                <div class="cel-fit">@include('hr.celebrations._card', ['c' => $c])</div>
                                {{ \App\Services\CelebrationService::DESIGNS[$type][$d] }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="cel-box">
                    <div class="cel-step-h"><span class="n">2</span><h2>Photo</h2></div>
            <div>
                <div class="d-flex align-items-center gap-2">
                    <div id="cel-thumb" class="cel-thumb" style="{{ $c0['photo'] ? "background-image:url('".e($c0['photo'])."');".$c0['photo_css'] : '' }}">{{ $c0['photo'] ? '' : $c0['initials'] }}</div>
                    <label class="btn btn-outline-primary mb-0 flex-fill" style="min-height:44px;display:flex;align-items:center;justify-content:center;gap:6px">
                        <i class="bx bx-camera"></i> <span id="cel-photo-label">{{ $c0['own_photo'] ? 'Change photo' : 'Upload a photo' }}</span>
                        <input id="cel-photo" type="file" name="photo" accept="image/*" class="d-none">
                    </label>
                    <button type="button" id="cel-photo-reset" class="btn btn-outline-secondary" style="min-height:44px;{{ $c0['own_photo'] ? '' : 'display:none' }}" title="Use the profile photo"><i class="bx bx-undo"></i></button>
                    <input type="hidden" name="remove_photo" id="cel-remove" value="0">
                    <input type="hidden" name="photo_pos" id="cel-pos" value="{{ $c0['zoom'] }},{{ $c0['px'] }},{{ $c0['py'] }}">
                </div>
                <div class="form-text">{{ $c0['profile_photo'] ? 'Uses the profile photo unless you upload one. A happy or candid photo works best.' : 'No profile photo, so the card shows initials until you upload one.' }}</div>
            </div>

            <div class="cel-adjust mt-3" id="cel-adjust" style="{{ $c0['photo'] ? '' : 'display:none' }}">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <span class="cel-sub">Adjust</span>
                    <button type="button" class="btn btn-sm btn-link p-0" id="cel-adj-reset">Reset</button>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-outline-secondary cel-step" data-zoom="-0.1" aria-label="Zoom out"><i class="bx bx-minus"></i></button>
                    <input type="range" class="form-range flex-fill" id="cel-zoom" min="1" max="4" step="0.05" value="{{ $c0['zoom'] }}" aria-label="Zoom">
                    <button type="button" class="btn btn-outline-secondary cel-step" data-zoom="0.1" aria-label="Zoom in"><i class="bx bx-plus"></i></button>
                </div>
                <div class="d-flex align-items-center gap-2 mt-2">
                    <span class="small text-muted">Move</span>
                    <button type="button" class="btn btn-outline-secondary cel-step" data-dx="-1" aria-label="Move left"><i class="bx bx-left-arrow-alt"></i></button>
                    <button type="button" class="btn btn-outline-secondary cel-step" data-dy="-1" aria-label="Move up"><i class="bx bx-up-arrow-alt"></i></button>
                    <button type="button" class="btn btn-outline-secondary cel-step" data-dy="1" aria-label="Move down"><i class="bx bx-down-arrow-alt"></i></button>
                    <button type="button" class="btn btn-outline-secondary cel-step" data-dx="1" aria-label="Move right"><i class="bx bx-right-arrow-alt"></i></button>
                </div>
                <div class="form-text">Or drag the photo on the card with your finger or mouse. Zoom in first to move it.</div>
            </div>

                </div>

                <div class="cel-box">
                    <div class="cel-step-h"><span class="n">3</span><h2>Message and date</h2></div>
                    <div class="mb-3">
                        <label class="form-label" for="cel-message">Message</label>
                        <textarea id="cel-message" name="message" class="form-control" rows="3" maxlength="400">{{ $c0['message'] }}</textarea>
                        <div class="form-text">Two or three lines fit the card best.</div>
                    </div>
                    <div>
                        <label class="form-label" for="cel-date">Show on Home on</label>
                        <input id="cel-date" type="date" name="post_date" class="form-control" value="{{ $date->toDateString() }}">
                    </div>
                </div>

                <div class="cel-actions">
                    <a href="{{ route('celebrations.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button class="btn btn-kp flex-fill" id="cel-submit"><i class="bx bx-send"></i> {{ $existing ? 'Save post' : 'Post to Home' }}</button>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="cel-pv" id="cel-pv" hidden>
    <div class="cel-pv-in">
        <div class="cel-pv-top"><b>Preview</b><span>This is exactly how the post and the downloaded image will look.</span></div>
        <div class="cel-fit" id="cel-pv-card"></div>
        <div class="d-flex gap-2 mt-3">
            <button type="button" class="btn btn-light flex-fill" id="cel-pv-close" style="min-height:44px">Back to edit</button>
            <button type="button" class="btn btn-outline-light flex-fill" id="cel-pv-dl" style="min-height:44px"><i class="bx bx-download"></i> Download</button>
            <button type="button" class="btn btn-kp flex-fill" id="cel-pv-post" style="min-height:44px"><i class="bx bx-send"></i> {{ $existing ? 'Save' : 'Post' }}</button>
        </div>
    </div>
</div>
<style>
    .cel-big .ph { cursor: grab; touch-action: none; }
    .cel-big .ph.dragging { cursor: grabbing; }
    .cel-step { min-width: 40px; min-height: 40px; padding: 0 8px; border-radius: 10px; }
    .cel-adjust { background: #f6f8fb; border-radius: 12px; padding: 10px 12px; }
    .cel-pv { position: fixed; inset: 0; z-index: 2000; background: rgba(1, 15, 36, .9); display: flex; align-items: center; justify-content: center; padding: 16px; overflow-y: auto; }
    .cel-pv[hidden] { display: none; }
    .cel-pv-in { width: 100%; max-width: 540px; }
    .cel-pv-top { color: #fff; margin-bottom: 10px; display: flex; flex-direction: column; }
    .cel-pv-top span { font-size: .8rem; color: #c9d3e6; }
    .cel-pv .cel-fit { max-width: 540px; margin: 0 auto; }
    .cel-pv .btn-kp { background: #c3b246; color: #011e48; font-weight: 700; border: 0; }
    .cel-pv .btn { border-radius: 10px; font-weight: 600; }
</style>
@include('hr.celebrations._scripts')
<script>
(function () {
    var msg = document.getElementById('cel-message');
    msg.addEventListener('input', function () {
        document.querySelectorAll('.cel-msg').forEach(function (el) { el.textContent = msg.value; });
    });
    document.querySelectorAll('input[name=design]').forEach(function (r) {
        r.addEventListener('change', function () {
            document.querySelectorAll('.cel-big').forEach(function (b) { b.classList.toggle('on', b.getAttribute('data-design') === r.value); });
            window.celFit();
        });
    });

    // Photo: which picture, how big (zoom) and where (x, y in %). Same numbers drive every design.
    var profile = @json($c0['profile_photo']), profileAspect = @json($c0['profile_aspect']), initials = @json($c0['initials']);
    var st = { url: @json($c0['photo']), a: @json($c0['aspect']), z: @json($c0['zoom']), x: @json($c0['px']), y: @json($c0['py']) };
    var file = document.getElementById('cel-photo'), reset = document.getElementById('cel-photo-reset'), remove = document.getElementById('cel-remove');
    var zoom = document.getElementById('cel-zoom'), pos = document.getElementById('cel-pos'), adjust = document.getElementById('cel-adjust');
    var clamp = function (v, lo, hi) { return Math.max(lo, Math.min(hi, v)); };
    // Like "cover" at zoom 1, for any frame shape (ea = frame width / height).
    function bgSize(ea) { ea = ea || 1; return st.a >= ea ? [100 * st.z * st.a / ea, 100 * st.z] : [100 * st.z, 100 * st.z * ea / st.a]; }
    function apply() {
        document.querySelectorAll('.cel-card .ph, #cel-thumb').forEach(function (el) {
            var s = bgSize(parseFloat(el.getAttribute('data-ea')) || 1);
            el.style.backgroundImage = st.url ? 'url("' + st.url + '")' : 'none';
            el.style.backgroundSize = s[0].toFixed(2) + '% ' + s[1].toFixed(2) + '%';
            el.style.backgroundPosition = st.x.toFixed(2) + '% ' + st.y.toFixed(2) + '%';
            el.textContent = st.url ? '' : initials;
        });
        zoom.value = st.z;
        pos.value = [st.z.toFixed(2), st.x.toFixed(2), st.y.toFixed(2)].join(',');
        adjust.style.display = st.url ? '' : 'none';
    }
    function setPhoto(url, a) { st.url = url; st.a = a || 1; st.z = 1; st.x = 50; st.y = 50; apply(); }

    file.addEventListener('change', function () {
        if (!file.files[0]) return;
        var r = new FileReader();
        r.onload = function () {
            var img = new Image();
            img.onload = function () { setPhoto(r.result, img.naturalWidth / img.naturalHeight); };
            img.src = r.result;
        };
        r.readAsDataURL(file.files[0]);
        remove.value = '0'; reset.style.display = '';
        document.getElementById('cel-photo-label').textContent = 'Change photo';
    });
    reset.addEventListener('click', function () {
        file.value = ''; remove.value = '1'; reset.style.display = 'none';
        document.getElementById('cel-photo-label').textContent = 'Upload a photo';
        setPhoto(profile, profileAspect);
    });
    zoom.addEventListener('input', function () { st.z = parseFloat(zoom.value); apply(); });
    document.getElementById('cel-adj-reset').addEventListener('click', function () { st.z = 1; st.x = 50; st.y = 50; apply(); });
    document.querySelectorAll('.cel-step').forEach(function (b) {
        b.addEventListener('click', function () {
            if (b.dataset.zoom) st.z = clamp(st.z + parseFloat(b.dataset.zoom), 1, 4);
            if (b.dataset.dx) st.x = clamp(st.x + 5 * b.dataset.dx, 0, 100);
            if (b.dataset.dy) st.y = clamp(st.y + 5 * b.dataset.dy, 0, 100);
            apply();
        });
    });

    // Drag the photo on the big card (mouse or finger).
    var drag = null;
    document.addEventListener('pointerdown', function (e) {
        var ph = e.target.closest('.cel-big .ph');
        if (!ph || !st.url) return;
        e.preventDefault();
        var r = ph.getBoundingClientRect(), s = bgSize(parseFloat(ph.getAttribute('data-ea')) || 1);
        drag = { el: ph, sx: e.clientX, sy: e.clientY, x: st.x, y: st.y,
                 ew: r.width * (s[0] / 100 - 1), eh: r.height * (s[1] / 100 - 1) };
        ph.classList.add('dragging');
        try { ph.setPointerCapture(e.pointerId); } catch (err) {}
    });
    document.addEventListener('pointermove', function (e) {
        if (!drag) return;
        if (drag.ew > 0.5) st.x = clamp(drag.x - (e.clientX - drag.sx) * 100 / drag.ew, 0, 100);
        if (drag.eh > 0.5) st.y = clamp(drag.y - (e.clientY - drag.sy) * 100 / drag.eh, 0, 100);
        apply();
    });
    ['pointerup', 'pointercancel'].forEach(function (t) {
        document.addEventListener(t, function () { if (drag) { drag.el.classList.remove('dragging'); drag = null; } });
    });

    // Full-size preview.
    var pv = document.getElementById('cel-pv'), pvCard = document.getElementById('cel-pv-card');
    var active = function () { return document.querySelector('.cel-big.on .cel-card'); };
    document.getElementById('cel-preview').addEventListener('click', function () {
        var copy = active().cloneNode(true);
        copy.style.transform = '';
        pvCard.innerHTML = ''; pvCard.appendChild(copy);
        pv.hidden = false; document.body.style.overflow = 'hidden';
        window.celFit();
    });
    var closePv = function () { pv.hidden = true; document.body.style.overflow = ''; };
    document.getElementById('cel-pv-close').addEventListener('click', closePv);
    pv.addEventListener('click', function (e) { if (e.target === pv) closePv(); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && !pv.hidden) closePv(); });
    document.getElementById('cel-pv-dl').addEventListener('click', function () { window.celDownload(active(), this); });
    document.getElementById('cel-pv-post').addEventListener('click', function () { document.getElementById('cel-submit').click(); });
    document.getElementById('cel-dl').addEventListener('click', function () { window.celDownload(active(), this); });

    apply();
})();
</script>
@endsection
