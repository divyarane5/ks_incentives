@use('App\Support\PeopleFormat', 'F')
@use('Illuminate\Support\Str')
@php
    $bu = $user->businessUnit;
    $buName = $bu?->name ?? '';
    $brand = $bu?->theme_color ?: '#011e48';
    $accent = $bu?->secondary_color ?: '#c3b246';
    $fullName = trim($user->first_name.' '.$user->last_name) ?: $user->name;
    $designation = $user->designation?->name;
    $phoneRaw = $user->official_contact ?: $user->personal_contact;
    $phone = F::intlPhone($phoneRaw);
    $email = $user->official_email ?: $user->email;
    $website = $bu?->domain ?? null;
    if ($website && ! Str::startsWith($website, ['http://', 'https://'])) {
        $website = 'https://'.$website;
    }
    $cardUrl = url()->current();
    $vcfUrl = route('user.vcf', $user->id);
    $shareText = "Here is my digital visiting card: tap to call, WhatsApp or save my number.\n\n".$cardUrl;
    $mapUrl = 'https://maps.app.goo.gl/8Vv9PSdNqZWvZ4RK8';
    $isRealEstate = Str::contains($buName, ['Real', 'Estate']);
    $isFinserv = ! $isRealEstate && Str::contains($buName, ['Finserv']);
    $logo = $bu?->logo && is_file(public_path('storage/'.$bu->logo)) ? asset('storage/'.$bu->logo) : null;
    $photo = $user->photo ? asset('storage/'.$user->photo) : null;
    $enquiry = $isRealEstate ? '919819777057' : $phone;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>{{ $fullName }}{{ $buName ? ' · '.$buName : '' }}</title>
    <meta name="description" content="{{ $fullName }}{{ $designation ? ', '.$designation : '' }}{{ $buName ? ' at '.$buName : '' }}. Call, WhatsApp or save the contact.">
    <meta property="og:title" content="{{ $fullName }}{{ $designation ? ' · '.$designation : '' }}">
    <meta property="og:description" content="{{ $buName ?: 'Digital visiting card' }}">
    @if($photo)<meta property="og:image" content="{{ $photo }}">@endif
    <meta name="theme-color" content="{{ $brand }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/boxicons/2.1.4/css/boxicons.min.css">
    <style>
        :root { --brand: {{ $brand }}; --accent: {{ $accent }}; --bg: #eef1f6; --text: #1f2a3d; --muted: #6b778c; --line: #e6eaf2; }
        * { box-sizing: border-box; }
        html, body { margin: 0; }
        body { font-family: "Public Sans", system-ui, -apple-system, "Segoe UI", Roboto, sans-serif; background: var(--bg); color: var(--text); -webkit-font-smoothing: antialiased; padding-bottom: calc(84px + env(safe-area-inset-bottom)); }
        a { color: inherit; text-decoration: none; }
        .wrap { max-width: 440px; margin: 0 auto; padding: 0 0 16px; }

        .top { background: var(--brand); color: #fff; padding: 22px 20px 74px; position: relative; overflow: hidden; border-radius: 0 0 26px 26px; }
        .top::before { content: ""; position: absolute; width: 260px; height: 260px; right: -90px; top: -110px; border-radius: 50%; background: color-mix(in srgb, var(--accent) 30%, transparent); }
        .top::after { content: ""; position: absolute; width: 160px; height: 160px; left: -70px; bottom: -80px; border-radius: 50%; background: color-mix(in srgb, #fff 7%, transparent); }
        .brandrow { position: relative; z-index: 1; display: flex; align-items: center; justify-content: space-between; gap: 10px; }
        .brandrow img { max-height: 34px; max-width: 60%; }
        .brandrow .co { font-weight: 700; font-size: .95rem; letter-spacing: .01em; }
        .iconbtn { width: 38px; height: 38px; border-radius: 50%; border: 0; background: color-mix(in srgb, #fff 14%, transparent); color: #fff; display: grid; place-items: center; font-size: 1.25rem; cursor: pointer; }

        .profile { background: #fff; margin: -56px 16px 0; border-radius: 20px; padding: 0 18px 18px; text-align: center; position: relative; z-index: 2; box-shadow: 0 10px 30px rgba(16, 24, 40, .08); }
        .photo { width: 112px; height: 112px; border-radius: 50%; object-fit: cover; border: 4px solid #fff; outline: 3px solid var(--accent); margin-top: -50px; background: #f4f6fa; }
        .initials { width: 112px; height: 112px; border-radius: 50%; border: 4px solid #fff; outline: 3px solid var(--accent); margin: -50px auto 0; background: var(--brand); color: #fff; display: grid; place-items: center; font-size: 2.2rem; font-weight: 800; }
        h1 { font-size: 1.45rem; margin: 12px 0 2px; color: var(--brand); font-weight: 800; line-height: 1.2; }
        .role { color: var(--muted); font-weight: 600; font-size: .95rem; }
        .co2 { display: inline-block; margin-top: 8px; padding: 4px 12px; border-radius: 99px; background: color-mix(in srgb, var(--accent) 18%, transparent); color: var(--brand); font-size: .78rem; font-weight: 700; }

        .quick { display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; margin-top: 18px; }
        .quick a, .quick button { display: flex; flex-direction: column; align-items: center; gap: 5px; padding: 10px 2px 8px; border-radius: 14px; background: #f4f6fa; border: 0; font: inherit; color: var(--brand); font-size: .72rem; font-weight: 700; cursor: pointer; }
        .quick i { font-size: 1.45rem; width: 42px; height: 42px; border-radius: 50%; display: grid; place-items: center; background: var(--brand); color: #fff; }
        .quick .wa i { background: #25d366; }
        .quick .off { opacity: .35; pointer-events: none; }

        .save { display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; margin-top: 14px; padding: 14px; border-radius: 14px; background: var(--accent); color: var(--brand); font-weight: 800; font-size: 1rem; border: 0; }

        .section { background: #fff; margin: 14px 16px 0; border-radius: 18px; padding: 16px 18px; box-shadow: 0 1px 3px rgba(16, 24, 40, .06); }
        .section h2 { margin: 0 0 10px; font-size: 1rem; color: var(--brand); font-weight: 800; display: flex; align-items: center; gap: 6px; }
        .section h2 i { color: var(--accent); font-size: 1.2rem; }
        .rows a, .rows div.row { display: flex; align-items: center; gap: 12px; padding: 10px 0; border-bottom: 1px solid var(--line); }
        .rows > :last-child { border-bottom: 0; }
        .rows i { width: 36px; height: 36px; border-radius: 10px; background: color-mix(in srgb, var(--brand) 8%, transparent); color: var(--brand); display: grid; place-items: center; font-size: 1.15rem; flex-shrink: 0; }
        .rows .l { font-size: .72rem; color: var(--muted); font-weight: 600; text-transform: uppercase; letter-spacing: .05em; }
        .rows .v { font-weight: 600; overflow-wrap: anywhere; }
        .rows .chev { margin-left: auto; background: none; width: auto; color: var(--muted); }
        .about p { margin: 0 0 10px; line-height: 1.55; color: #3a4558; font-size: .92rem; }
        .about h3 { font-size: .8rem; text-transform: uppercase; letter-spacing: .06em; color: var(--muted); margin: 12px 0 4px; }
        .values { display: flex; flex-wrap: wrap; gap: 6px; }
        .values span { background: #f4f6fa; border-radius: 99px; padding: 4px 10px; font-size: .8rem; font-weight: 600; color: var(--brand); }
        .products { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        .product { border: 1px solid var(--line); border-radius: 14px; padding: 10px; text-align: center; display: flex; flex-direction: column; gap: 8px; }
        .product img { width: 100%; height: 76px; object-fit: contain; }
        .product .n { font-weight: 700; font-size: .82rem; color: var(--brand); flex: 1; }
        .product .b { display: block; padding: 8px; border-radius: 10px; background: var(--brand); color: #fff; font-size: .75rem; font-weight: 700; }
        .foot { text-align: center; color: var(--muted); font-size: .72rem; margin: 18px 0 0; }

        .bar { position: fixed; left: 0; right: 0; bottom: 0; z-index: 50; background: #fff; border-top: 1px solid var(--line); padding: 10px 16px calc(10px + env(safe-area-inset-bottom)); display: flex; gap: 10px; justify-content: center; box-shadow: 0 -6px 20px rgba(16, 24, 40, .08); }
        .bar a { flex: 1; max-width: 200px; display: flex; align-items: center; justify-content: center; gap: 6px; padding: 13px; border-radius: 14px; font-weight: 800; color: #fff; background: var(--brand); }
        .bar a.wa { background: #25d366; }
        .bar i { font-size: 1.3rem; }

        .sheet-bg { position: fixed; inset: 0; background: rgba(10, 16, 30, .5); z-index: 90; display: none; }
        .sheet { position: fixed; left: 0; right: 0; bottom: 0; z-index: 100; background: #fff; border-radius: 22px 22px 0 0; padding: 10px 20px calc(22px + env(safe-area-inset-bottom)); max-width: 480px; margin: 0 auto; transform: translateY(110%); transition: transform .25s ease; }
        .sheet.open { transform: none; }
        .sheet .grab { width: 40px; height: 4px; border-radius: 4px; background: var(--line); margin: 0 auto 12px; }
        .sheet h3 { margin: 0 0 4px; color: var(--brand); font-size: 1.05rem; text-align: center; }
        .sheet .sub { text-align: center; color: var(--muted); font-size: .82rem; margin-bottom: 12px; }
        .seg { display: flex; background: #f4f6fa; border-radius: 12px; padding: 4px; margin-bottom: 14px; }
        .seg button { flex: 1; border: 0; background: none; padding: 9px; border-radius: 9px; font: inherit; font-weight: 700; font-size: .85rem; color: var(--muted); cursor: pointer; }
        .seg button.on { background: #fff; color: var(--brand); box-shadow: 0 1px 3px rgba(16, 24, 40, .1); }
        #qr { display: grid; place-items: center; padding: 12px; background: #fff; border: 1px solid var(--line); border-radius: 16px; width: 224px; height: 224px; margin: 0 auto; }
        #qr img, #qr canvas { width: 200px !important; height: 200px !important; }
        .sheet .acts { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 14px; }
        .sheet .acts a, .sheet .acts button { display: flex; align-items: center; justify-content: center; gap: 6px; padding: 12px; border-radius: 12px; border: 1px solid var(--line); background: #fff; font: inherit; font-weight: 700; color: var(--brand); font-size: .85rem; cursor: pointer; }
        .toast { position: fixed; left: 50%; bottom: 100px; transform: translateX(-50%); background: #1f2a3d; color: #fff; padding: 9px 16px; border-radius: 99px; font-size: .85rem; z-index: 120; display: none; }
    </style>
</head>
<body>
<div class="wrap">
    <header class="top">
        <div class="brandrow">
            @if($logo)
                <img src="{{ $logo }}" alt="{{ $buName }}">
            @else
                <span class="co">{{ $buName }}</span>
            @endif
            <button class="iconbtn" type="button" onclick="openSheet('profile')" aria-label="Share or show QR code"><i class="bx bx-qr"></i></button>
        </div>
    </header>

    <main>
        <section class="profile">
            @if($photo)
                <img class="photo" src="{{ $photo }}" alt="{{ $fullName }}">
            @else
                <div class="initials">{{ F::initials($fullName) }}</div>
            @endif
            <h1>{{ $fullName }}</h1>
            @if($designation)<div class="role">{{ $designation }}</div>@endif
            @if($buName)<div class="co2">{{ $buName }}</div>@endif

            <div class="quick">
                <a href="{{ $phone ? 'tel:+'.$phone : '#' }}" class="{{ $phone ? '' : 'off' }}"><i class="bx bx-phone"></i>Call</a>
                <a href="{{ $phone ? 'https://wa.me/'.$phone : '#' }}" class="wa {{ $phone ? '' : 'off' }}" target="_blank" rel="noopener"><i class="bx bxl-whatsapp"></i>WhatsApp</a>
                <a href="{{ $email ? 'mailto:'.$email : '#' }}" class="{{ $email ? '' : 'off' }}"><i class="bx bx-envelope"></i>Email</a>
                <button type="button" onclick="shareCard()"><i class="bx bx-share-alt"></i>Share</button>
            </div>

            <a class="save" href="{{ $vcfUrl }}"><i class="bx bx-user-plus"></i> Save to contacts</a>
        </section>

        <section class="section">
            <h2><i class="bx bx-id-card"></i>Contact</h2>
            <div class="rows">
                @if($phone)
                    <a href="tel:+{{ $phone }}"><i class="bx bx-phone"></i><div><div class="l">Mobile</div><div class="v">+{{ substr($phone, 0, 2) }} {{ substr($phone, 2) }}</div></div><i class="bx bx-chevron-right chev"></i></a>
                @endif
                @if($email)
                    <a href="mailto:{{ $email }}"><i class="bx bx-envelope"></i><div><div class="l">Email</div><div class="v">{{ $email }}</div></div><i class="bx bx-chevron-right chev"></i></a>
                @endif
                @if($website)
                    <a href="{{ $website }}" target="_blank" rel="noopener"><i class="bx bx-globe"></i><div><div class="l">Website</div><div class="v">{{ preg_replace('#^https?://(www\.)?#', '', rtrim($website, '/')) }}</div></div><i class="bx bx-chevron-right chev"></i></a>
                @endif
                <a href="{{ $mapUrl }}" target="_blank" rel="noopener"><i class="bx bx-map"></i><div><div class="l">Office</div><div class="v">{{ $user->location?->name ?: 'Get directions' }}</div></div><i class="bx bx-chevron-right chev"></i></a>
            </div>
        </section>

        @if($isRealEstate)
            <section class="section about">
                <h2><i class="bx bx-buildings"></i>About us</h2>
                <p>Keystone Real Estate Advisory brings you a wide range of properties across Mumbai and Pune. With over 10 years of expertise, we help you find the perfect home for your needs and lifestyle.</p>
                <h3>Mission</h3>
                <p>To deliver the best value, exceptional service and satisfaction for our clients and associates.</p>
                <h3>Vision</h3>
                <p>To be the world's deeply loved, efficient and valuable people-centric real estate company.</p>
                <h3>Values</h3>
                <div class="values"><span>Innovative</span><span>Ethical</span><span>Reliable</span><span>Integrity</span><span>Committed</span></div>
                <div class="rows" style="margin-top:12px">
                    <a href="https://keystonerealestateadvisory.com/KeystoneDeck.pdf" target="_blank" rel="noopener"><i class="bx bx-file"></i><div><div class="v">Our company profile</div></div><i class="bx bx-chevron-right chev"></i></a>
                </div>
            </section>

            <section class="section">
                <h2><i class="bx bx-grid-alt"></i>End-to-end real estate solutions</h2>
                <div class="products">
                    @foreach([
                        ['Alterra India', 'assets/img/icons/Untitled-1-03.jpg', null],
                        ['Keystone Real Estate Advisory', 'assets/img/icons/keystone-04.jpg', 'https://keystonerealestateadvisory.com/'],
                        ['Keystone Finserv', 'assets/img/icons/kf.jpg', 'http://keystonefinserv.com/'],
                        ['Designo Palette', 'assets/img/icons/Untitled-1-02.jpg', 'http://designopalette.com/'],
                    ] as [$n, $img, $url])
                        <div class="product">
                            @if($url)<a href="{{ $url }}" target="_blank" rel="noopener"><img src="{{ asset($img) }}" alt="{{ $n }}"></a>@else<img src="{{ asset($img) }}" alt="{{ $n }}">@endif
                            <div class="n">{{ $n }}</div>
                            <a class="b" href="https://wa.me/{{ $enquiry }}?text={{ rawurlencode('Enquiry for: '.$n) }}" target="_blank" rel="noopener">Enquire</a>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="section">
                <h2><i class="bx bx-hard-hat"></i>Our construction associates</h2>
                <div class="rows">
                    <a href="https://keystonerealestateadvisory.com/aum-sai-company-profile.pdf" target="_blank" rel="noopener"><i class="bx bx-link"></i><div><div class="v">Aum Sai Constructions</div></div><i class="bx bx-chevron-right chev"></i></a>
                    <a href="https://keystonerealestateadvisory.com/AJ-Constructions-profile.pdf" target="_blank" rel="noopener"><i class="bx bx-link"></i><div><div class="v">AJ Constructions</div></div><i class="bx bx-chevron-right chev"></i></a>
                </div>
            </section>
        @elseif($isFinserv)
            <section class="section about">
                <h2><i class="bx bx-bank"></i>About us</h2>
                <p>Keystone Finserv provides fast, tailored financial solutions through leading banking partners. We simplify the loan process and support clients at every step.</p>
                <h3>Mission</h3>
                <p>Trusted, transparent, client-focused financial guidance.</p>
                <h3>Vision</h3>
                <p>Innovative, reliable and service-driven financial partnership.</p>
            </section>

            <section class="section">
                <h2><i class="bx bx-grid-alt"></i>Our solutions</h2>
                <div class="products">
                    @foreach([
                        ['Home Loan', 'assets/img/icons/house.png'],
                        ['Property Search', 'assets/img/icons/real-estate.png'],
                        ['Loan Against Property', 'assets/img/icons/mortgage-loan.png'],
                        ['Personal Loan', 'assets/img/icons/personal.png'],
                        ['Business Loan', 'assets/img/icons/money.png'],
                    ] as [$n, $img])
                        <div class="product">
                            <img src="{{ asset($img) }}" alt="{{ $n }}">
                            <div class="n">{{ $n }}</div>
                            @if($enquiry)<a class="b" href="https://wa.me/{{ $enquiry }}?text={{ rawurlencode('Enquiry for: '.$n) }}" target="_blank" rel="noopener">Enquire</a>@endif
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        <p class="foot">{{ $buName }} · Digital visiting card</p>
    </main>
</div>

@if($phone)
<nav class="bar">
    <a href="tel:+{{ $phone }}"><i class="bx bx-phone"></i>Call</a>
    <a class="wa" href="https://wa.me/{{ $phone }}" target="_blank" rel="noopener"><i class="bx bxl-whatsapp"></i>WhatsApp</a>
</nav>
@endif

<div class="sheet-bg" id="sheetBg" onclick="closeSheet()"></div>
<div class="sheet" id="sheet" role="dialog" aria-modal="true" aria-label="QR code">
    <div class="grab"></div>
    <h3>Scan to connect</h3>
    <div class="sub">{{ $fullName }}{{ $buName ? ' · '.$buName : '' }}</div>
    <div class="seg">
        <button type="button" id="segProfile" class="on" onclick="showQr('profile')">This card</button>
        <button type="button" id="segContact" onclick="showQr('contact')">Save contact</button>
    </div>
    <div id="qr"></div>
    <div class="acts">
        <button type="button" onclick="downloadQr()"><i class="bx bx-download"></i>Download QR</button>
        <button type="button" onclick="copyLink()"><i class="bx bx-copy"></i>Copy link</button>
    </div>
</div>
<div class="toast" id="toast"></div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
var CARD = { url: @json($cardUrl), vcf: @json($vcfUrl), name: @json($fullName), text: @json($shareText), slug: @json(Str::slug($fullName)) };
var qrMode = 'profile', qrObj = null;

function toast(msg) {
    var t = document.getElementById('toast');
    t.textContent = msg; t.style.display = 'block';
    clearTimeout(t._h); t._h = setTimeout(function () { t.style.display = 'none'; }, 1800);
}
function shareCard() {
    if (navigator.share) {
        navigator.share({ title: CARD.name, text: 'Here is my digital visiting card', url: CARD.url }).catch(function () {});
    } else {
        window.open('https://wa.me/?text=' + encodeURIComponent(CARD.text), '_blank');
    }
}
function openSheet(mode) {
    document.getElementById('sheetBg').style.display = 'block';
    requestAnimationFrame(function () { document.getElementById('sheet').classList.add('open'); });
    showQr(mode || 'profile');
}
function closeSheet() {
    document.getElementById('sheet').classList.remove('open');
    setTimeout(function () { document.getElementById('sheetBg').style.display = 'none'; }, 220);
}
function showQr(mode) {
    qrMode = mode;
    document.getElementById('segProfile').classList.toggle('on', mode === 'profile');
    document.getElementById('segContact').classList.toggle('on', mode === 'contact');
    var box = document.getElementById('qr');
    box.innerHTML = '';
    if (typeof QRCode === 'undefined') { box.textContent = 'QR code could not load.'; return; }
    qrObj = new QRCode(box, { text: mode === 'profile' ? CARD.url : CARD.vcf, width: 400, height: 400, colorDark: getComputedStyle(document.documentElement).getPropertyValue('--brand').trim() || '#011e48', colorLight: '#ffffff', correctLevel: QRCode.CorrectLevel.M });
}
function downloadQr() {
    var box = document.getElementById('qr');
    var canvas = box.querySelector('canvas');
    var img = box.querySelector('img');
    var src = canvas ? canvas.toDataURL('image/png') : (img ? img.src : null);
    if (!src) return;
    var a = document.createElement('a');
    a.href = src; a.download = CARD.slug + (qrMode === 'profile' ? '-card' : '-contact') + '-qr.png';
    document.body.appendChild(a); a.click(); a.remove();
}
function copyLink() {
    var link = qrMode === 'profile' ? CARD.url : CARD.vcf;
    if (navigator.clipboard) { navigator.clipboard.writeText(link).then(function () { toast('Link copied'); }, function () { prompt('Copy this link', link); }); }
    else { prompt('Copy this link', link); }
}
document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeSheet(); });
</script>
</body>
</html>
