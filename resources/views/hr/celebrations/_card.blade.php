{{-- One celebration card, drawn at 540 x 540 (downloads at 1080 x 1080). $c from CelebrationService::card(). --}}
@once
<style>
    @font-face { font-family: "CelScript"; src: url("{{ asset('assets/celebrations/fonts/great-vibes-latin-400-normal.woff2') }}") format("woff2"); font-weight: 400; font-display: block; }
    @font-face { font-family: "CelSerif"; src: url("{{ asset('assets/celebrations/fonts/playfair-display-latin-700-normal.woff2') }}") format("woff2"); font-weight: 700; font-display: block; }
    @font-face { font-family: "CelCaps"; src: url("{{ asset('assets/celebrations/fonts/cinzel-latin-600-normal.woff2') }}") format("woff2"); font-weight: 600; font-display: block; }
    @font-face { font-family: "CelSans"; src: url("{{ asset('assets/celebrations/fonts/montserrat-latin-500-normal.woff2') }}") format("woff2"); font-weight: 500; font-display: block; }
    @font-face { font-family: "CelSans"; src: url("{{ asset('assets/celebrations/fonts/montserrat-latin-700-normal.woff2') }}") format("woff2"); font-weight: 700; font-display: block; }
    .cel-fit { position: relative; width: 100%; max-width: 540px; overflow: hidden; border-radius: 14px; box-shadow: 0 6px 20px rgba(1, 30, 72, .15); }
    .cel-fit { aspect-ratio: 1 / 1; }
    .cel-fit > .cel-card { position: absolute; top: 0; left: 0; transform-origin: 0 0; }
    .cel-card { width: 540px; height: 540px; position: relative; overflow: hidden; box-sizing: border-box; font-family: "CelSans", "Public Sans", Arial, sans-serif; font-weight: 500; text-align: center; color: #fff; }
    .cel-card * { box-sizing: border-box; }
    .cel-card .ab { position: absolute; }
    .cel-card .serif { font-family: "CelSerif", Georgia, "Times New Roman", serif; font-weight: 700; }
    .cel-card .script { font-family: "CelScript", "Brush Script MT", cursive; font-weight: 400; }
    .cel-card .caps { font-family: "CelCaps", Georgia, serif; font-weight: 600; }
    .cel-card .ph { position: absolute; border-radius: 50%; background-repeat: no-repeat; background-size: cover; background-position: center; background-color: #e8ecf4; display: flex; align-items: center; justify-content: center; font-weight: 700; color: #011e48; }
    .cel-card .nm { position: absolute; left: 30px; right: 30px; font-weight: 700; line-height: 1.15; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .cel-card .ds { position: absolute; left: 30px; right: 30px; font-size: 15px; font-weight: 700; }
    .cel-card .ms { position: absolute; left: 60px; right: 60px; font-size: 14.5px; line-height: 1.45; }
    .cel-card .co { position: absolute; left: 0; right: 0; font-size: 12px; font-weight: 700; letter-spacing: 3px; text-transform: uppercase; }
    .cel-card .lg { position: absolute; display: block; }
    .cel-card .dot { position: absolute; border-radius: 50%; }
    .cel-card .bit { position: absolute; width: 14px; height: 6px; border-radius: 2px; }
    .cel-card svg { position: absolute; overflow: visible; }
</style>
@endonce
@php
    $CS = \App\Services\CelebrationService::class;
    // Photo frame: width, height, border, extra css, corner radius. Zoom/position work the same in every frame shape.
    $photo = function ($w, $h, $border, $extra = '', $radius = '50%') use ($c, $CS) {
        $fit = $c['photo'] ? 'background-image:url(\''.e($c['photo']).'\');'.$CS::photoCss($c['aspect'] ?? 1, $c['zoom'] ?? 1, $c['px'] ?? 50, $c['py'] ?? 50, $w / $h) : '';

        return '<div class="ph" data-ea="'.round($w / $h, 4).'" style="width:'.$w.'px;height:'.$h.'px;border-radius:'.$radius.';font-size:'.round(min($w, $h) / 3).'px;border:'.$border.';'.$extra.$fit.'">'.($c['photo'] ? '' : e($c['initials'])).'</div>';
    };
    // Company logo (Keystone) or the company name in its place.
    $logo = function ($tone, $h, $style, $textColor = null) use ($c) {
        $src = $tone === 'light' ? $c['logo_light'] ?? null : $c['logo_dark'] ?? null;
        if ($src) {
            return '<img class="lg" src="'.e($src).'" alt="" style="height:'.$h.'px;'.$style.'">';
        }

        return '<div class="lg" style="'.$style.';font-weight:700;font-size:'.max(11, round($h / 3)).'px;letter-spacing:1px;line-height:1.2;color:'.($textColor ?? ($tone === 'light' ? '#fff' : '#011e48')).'">'.e($c['company']).'</div>';
    };
    $y = (int) ($c['years'] ?? 0);
    $suf = $c['suffix'] ?? $CS::suffix($y);
    $yrs = $y ? $CS::yearsWord($y) : '';
    $len = mb_strlen($c['name']);
    $nameSize = fn ($base, $width) => min($base, (int) floor($width * 1.85 / max(1, $len)));
@endphp
<div class="cel-card cel-{{ $c['type'] }}-{{ $c['design'] }}" data-file="{{ $c['file'] }}">
@if($c['type'] === 'birthday' && $c['design'] === 4)
    {{-- Keystone birthday: logo, script "Birthday", portrait photo on navy, four wishes. --}}
    <div style="position:absolute;inset:0;background:linear-gradient(160deg,#ffffff 0%,#f6f7f9 60%,#eef1f4 100%)"></div>
    <svg width="540" height="540" style="left:0;top:0" viewBox="0 0 540 540">
        <defs><filter id="lf{{ $c['file'] }}" x="-50%" y="-50%" width="200%" height="200%"><feGaussianBlur stdDeviation="6"/></filter></defs>
        <g filter="url(#lf{{ $c['file'] }})" opacity=".85">
            <path d="M-20 120 C 20 100, 45 130, 20 170 C 0 190, -20 170, -20 120 Z" fill="#5d9b32"/>
            <path d="M-10 230 C 30 220, 40 260, 5 290 C -15 300, -25 270, -10 230 Z" fill="#7cb342"/>
            <path d="M-20 440 C 40 410, 80 460, 40 520 C 10 550, -30 520, -20 440 Z" fill="#558b2f"/>
            <path d="M40 520 C 80 480, 120 500, 110 545 L 40 545 Z" fill="#8bc34a"/>
            <path d="M560 330 C 510 330, 495 380, 530 410 C 550 420, 565 390, 560 330 Z" fill="#6aa83a"/>
            <path d="M470 545 C 480 490, 530 470, 560 480 L 560 545 Z" fill="#7cb342"/>
            <path d="M500 520 C 470 500, 470 460, 520 450 C 540 470, 530 510, 500 520 Z" fill="#558b2f"/>
        </g>
    </svg>
    {{-- navy band behind the photo, with a gold edge --}}
    <div class="ab" style="left:338px;top:-6px;width:60px;height:100px;border:3px solid #f2b51c;border-top:0;border-radius:0 0 0 30px"></div>
    <div class="ab" style="left:342px;top:0;width:52px;height:92px;background:#011e48;border-radius:0 0 0 26px"></div>
    <div class="ab" style="left:338px;top:86px;width:220px;height:385px;border:3px solid #f2b51c;border-right:0;border-radius:44px 0 0 44px"></div>
    <div class="ab" style="left:343px;top:91px;width:210px;height:375px;background:#011e48;border-radius:40px 0 0 40px"></div>
    <div class="ab script" style="left:432px;top:10px;width:110px;font-size:21px;line-height:.95;color:#011e48;text-align:left;transform:rotate(-8deg)">People<br>&nbsp;Build<br>Possibilities</div>
    <svg width="70" height="12" style="left:462px;top:76px" viewBox="0 0 70 12"><path d="M2 10 C 25 3, 45 2, 68 4" stroke="#f2b51c" stroke-width="2" fill="none" stroke-linecap="round"/></svg>
    {{-- photo with gold corner line --}}
    <div class="ab" style="left:272px;top:84px;width:244px;height:344px;border:3px solid #f2b51c;border-right:0;border-bottom:0;border-radius:30px 0 0 0"></div>
    {!! $photo(232, 336, '4px solid #fff', 'left:280px;top:90px;box-shadow:0 8px 24px rgba(1,30,72,.25);', '26px 26px 26px 26px') !!}

    {!! $logo('dark', 54, 'left:40px;top:28px;max-width:190px;text-align:left') !!}
    <div class="ab" style="left:20px;width:250px;top:118px;font-size:27px;font-weight:700;letter-spacing:9px;color:#011e48">HAPPY</div>
    <div class="ab script" style="left:10px;width:270px;top:150px;font-size:86px;line-height:1.05;color:#011e48">Birthday</div>
    <svg width="160" height="16" style="left:70px;top:244px" viewBox="0 0 160 16"><path d="M3 13 C 50 3, 110 1, 157 5" stroke="#f2b51c" stroke-width="4" fill="none" stroke-linecap="round"/></svg>
    <svg width="34" height="30" style="left:222px;top:146px" viewBox="0 0 34 30">
        <path d="M17 26 C 6 18, 2 12, 4 8 C 6 3, 13 3, 17 9 C 21 3, 28 3, 30 8 C 32 12, 28 18, 17 26 Z" stroke="#f2b51c" stroke-width="2.6" fill="none" stroke-linejoin="round"/>
        <path d="M24 -4 L 22 1 M31 -1 L 27 3 M34 6 L 29 6" stroke="#f2b51c" stroke-width="2" stroke-linecap="round"/>
    </svg>
    <div class="ab" style="left:14px;width:262px;top:268px;display:flex;align-items:center;justify-content:center;gap:7px;color:#011e48">
        <span style="width:14px;height:2px;background:#f2b51c;flex-shrink:0"></span>
        <span style="font-size:{{ $nameSize(21, 220) }}px;font-weight:700;white-space:nowrap">{{ $c['name'] }}</span>
        <span style="width:14px;height:2px;background:#f2b51c;flex-shrink:0"></span>
    </div>
    @if($c['designation'])<div class="ab" style="left:20px;width:250px;top:296px;font-size:11.5px;font-weight:700;color:#b8860b;letter-spacing:.5px">{{ $c['designation'] }}</div>@endif
    <div class="ab cel-msg" style="left:42px;width:206px;top:318px;font-size:13px;line-height:1.4;color:#011e48">{{ $c['message'] }}</div>
    <div class="ab script" style="left:30px;width:230px;top:388px;font-size:38px;color:#f2b51c;transform:rotate(-7deg)">Keep shining!</div>
    <div class="ab" style="left:28px;top:452px;width:300px;display:flex;align-items:flex-start;color:#011e48">
        @foreach([['BIGGER','GOALS','M3 20h16 M5 16v-3 M9 16v-6 M13 16v-8 M17 16v-11 M4 9 L 9 5 L 12 7 L 18 2 M14 2h4v4'], ['BETTER','HEALTH','M11 19 C 3 13, 1 9, 3 5 C 5 1, 9 2, 11 6 C 13 2, 17 1, 19 5 C 21 9, 19 13, 11 19 Z'], ['BRIGHTER','DAYS','M11 2 L 13.6 8 L 20 8.6 L 15 12.8 L 16.6 19 L 11 15.6 L 5.4 19 L 7 12.8 L 2 8.6 L 8.4 8 Z'], ['GREATEST','SUCCESS','M11 8 a3 3 0 1 0 0.01 0 M5 10 a2.3 2.3 0 1 0 0.01 0 M17 10 a2.3 2.3 0 1 0 0.01 0 M6 19 c0-4 2.5-6 5-6 s5 2 5 6 M1 18 c0-3 1.6-4.4 3.6-4.4 M21 18 c0-3-1.6-4.4-3.6-4.4']] as $i => [$a, $b, $d])
            @if($i)<span style="width:2px;height:40px;background:#f2b51c;margin-top:4px;flex-shrink:0"></span>@endif
            <div style="flex:1;text-align:center">
                <svg width="22" height="22" viewBox="0 0 22 22" style="position:static;display:block;margin:0 auto 3px"><path d="{{ $d }}" stroke="#f2b51c" stroke-width="1.8" fill="none" stroke-linecap="round" stroke-linejoin="round"/></svg>
                <div style="font-size:8.5px;font-weight:700;line-height:1.15">{{ $a }}<br>{{ $b }}</div>
            </div>
        @endforeach
    </div>

@elseif($c['type'] === 'anniversary' && $c['design'] === 4)
    {{-- Keystone work anniversary: navy waves, gold number with laurels and ribbon, arch photo, name plate. --}}
    <div style="position:absolute;inset:0;background:linear-gradient(120deg,#ffffff 0%,#f7f4ee 55%,#efe9df 100%)"></div>
    <svg width="230" height="70" style="left:8px;top:468px;opacity:.35" viewBox="0 0 230 70">
        <path d="M0 70 V48 h10 V36 h8 V48 h6 V20 h12 V48 h8 V30 h10 V12 h6 V30 h8 V48 h10 V26 h14 V48 h8 V38 h10 V48 h6 V16 h10 V8 h4 V16 h10 V48 h10 V34 h12 V48 h8 V28 h10 V48 h10 V40 h12 V70 Z" fill="none" stroke="#c9a04a" stroke-width="1"/>
    </svg>
    <svg width="540" height="90" style="left:0;top:0" viewBox="0 0 540 90">
        <path d="M0 0 H340 C 300 40, 250 66, 170 72 C 100 76, 40 78, 0 88 Z" fill="#011e48"/>
        <path d="M0 88 C 40 78, 100 76, 170 72 C 250 66, 300 40, 340 0" stroke="#d4a537" stroke-width="3" fill="none"/>
    </svg>
    <svg width="320" height="130" style="left:220px;top:412px" viewBox="0 0 320 130">
        <path d="M320 0 V130 H0 C 90 118, 180 112, 240 80 C 280 58, 300 30, 320 0 Z" fill="#011e48"/>
        <path d="M0 130 C 90 118, 180 112, 240 80 C 280 58, 300 30, 320 0" stroke="#d4a537" stroke-width="3" fill="none"/>
    </svg>
    {!! $logo('light', 50, 'left:34px;top:10px;max-width:190px;text-align:left') !!}

    {{-- arch photo --}}
    <div class="ab" style="left:228px;top:44px;width:292px;height:400px;border:2px solid #d4a537;border-bottom:0;border-radius:146px 146px 0 0"></div>
    {!! $photo(272, 396, '3px solid #d4a537', 'left:238px;top:54px;', '136px 136px 0 0') !!}
    <div class="ab" style="left:222px;top:428px;width:290px;height:82px;background:#011e48;border:2px solid #d4a537;border-radius:24px;box-shadow:0 6px 18px rgba(1,30,72,.3)"></div>
    <div class="ab serif" style="left:228px;width:278px;top:436px;font-size:{{ $nameSize(28, 250) }}px;color:#fff;white-space:nowrap;overflow:hidden">{{ $c['name'] }}</div>
    @if($c['designation'])
        <div class="ab" style="left:236px;width:262px;top:476px;display:flex;align-items:center;justify-content:center;gap:8px">
            <span style="flex:1;height:1.5px;background:#d4a537"></span>
            <span style="background:linear-gradient(180deg,#f8dd8c,#d9a93c);color:#011e48;font-weight:700;font-size:12px;padding:3px 14px;border-radius:999px;white-space:nowrap;max-width:200px;overflow:hidden;text-overflow:ellipsis">{{ $c['designation'] }}</span>
            <span style="flex:1;height:1.5px;background:#d4a537"></span>
        </div>
    @endif

    <div class="ab script" style="left:10px;width:230px;top:80px;font-size:52px;line-height:1;color:#c8962e">Happy</div>
    <div class="ab serif" style="left:10px;width:230px;top:130px;font-size:52px;line-height:1;color:#011e48;letter-spacing:-1px">WORK</div>
    <div class="ab caps" style="left:10px;width:230px;top:186px;font-size:21px;letter-spacing:1px;color:#011e48">ANNIVERSARY</div>
    {{-- laurels --}}
    @foreach([['left:30px', 1], ['left:178px', -1]] as [$pos, $flip])
        <svg width="40" height="84" style="{{ $pos }};top:242px;transform:scaleX({{ $flip }})" viewBox="0 0 40 84">
            <path d="M30 82 C 12 66, 6 40, 18 6" stroke="#c8962e" stroke-width="2" fill="none"/>
            @foreach([[18,10,-40],[13,22,-30],[10,35,-20],[10,48,-10],[13,61,0],[19,72,15]] as [$lx,$ly,$r])
                <ellipse cx="{{ $lx - 4 }}" cy="{{ $ly }}" rx="7" ry="3.2" fill="#d4a537" transform="rotate({{ $r - 30 }} {{ $lx - 4 }} {{ $ly }})"/>
                <ellipse cx="{{ $lx + 5 }}" cy="{{ $ly + 3 }}" rx="7" ry="3.2" fill="#e0b54a" transform="rotate({{ $r + 40 }} {{ $lx + 5 }} {{ $ly + 3 }})"/>
            @endforeach
        </svg>
    @endforeach
    <div class="ab serif" style="left:60px;width:110px;top:218px;font-size:{{ $y >= 10 ? 92 : 112 }}px;line-height:1;color:#d4a537;text-shadow:0 2px 0 #9c6f12,0 4px 8px rgba(120,80,0,.35)">{{ $y }}</div>
    <div class="ab serif" style="left:{{ $y >= 10 ? 166 : 148 }}px;top:226px;font-size:26px;color:#b8860b">{{ $suf }}</div>
    <svg width="210" height="54" style="left:16px;top:318px" viewBox="0 0 210 54">
        <path d="M2 30 L 26 26 L 22 40 L 30 50 L 4 46 L 12 38 Z" fill="#b8860b"/>
        <path d="M208 22 L 184 22 L 188 36 L 180 46 L 206 40 L 198 32 Z" fill="#b8860b"/>
        <path d="M22 20 C 70 8, 140 6, 190 14 L 190 38 C 140 30, 70 32, 22 44 Z" fill="#e2b445" stroke="#b8860b" stroke-width="1"/>
    </svg>
    <div class="ab caps" style="left:30px;width:180px;top:330px;font-size:15px;letter-spacing:1.5px;color:#011e48;transform:rotate(-3deg)">ANNIVERSARY</div>
    <div class="ab" style="left:60px;width:110px;top:368px;font-size:15px;letter-spacing:4px;color:#d4a537">★★★</div>
    <div class="ab" style="left:30px;width:196px;top:390px;font-size:14.5px;font-weight:700;line-height:1.3;color:#011e48;text-align:left">Congratulations on completing {{ $y }} successful {{ $y === 1 ? 'year' : 'years' }} with us!</div>
    <div class="ab" style="left:30px;top:452px;width:78px;height:2px;background:#d4a537"></div>
    <div class="ab cel-msg" style="left:30px;width:196px;top:460px;font-size:11.5px;line-height:1.35;color:#1f2a3d;text-align:left">{{ $c['message'] }}</div>

@elseif($c['type'] === 'birthday' && $c['design'] === 1)
    <div style="position:absolute;inset:0;background:#011e48"></div>
    <div class="dot" style="width:260px;height:260px;right:-90px;top:-90px;background:rgba(195,178,70,.18)"></div>
    <div class="dot" style="width:200px;height:200px;left:-80px;bottom:-70px;background:rgba(195,178,70,.12)"></div>
    @foreach([[60,70,'#c3b246',10],[470,140,'#fff',8],[90,300,'#fff',6],[450,330,'#c3b246',12],[40,200,'#c3b246',7],[500,250,'#fff',5],[30,40,'#fff',6],[500,40,'#c3b246',8]] as [$dx,$dy,$col,$s])
        <div class="dot" style="left:{{ $dx }}px;top:{{ $dy }}px;width:{{ $s }}px;height:{{ $s }}px;background:{{ $col }}"></div>
    @endforeach
    <div class="ab script" style="left:0;right:0;top:22px;font-size:62px;color:#c3b246">Happy Birthday</div>
    {!! $photo(180, 180, '6px solid #c3b246', 'left:180px;top:104px;') !!}
    <div class="nm" style="top:298px;font-size:{{ $nameSize(30, 440) }}px">{{ $c['name'] }}</div>
    <div class="ds" style="top:336px;color:#c9d3e6">{{ $c['designation'] }}</div>
    <div class="ms cel-msg" style="top:364px;color:#fff">{{ $c['message'] }}</div>
    <div class="ab script" style="left:0;right:0;top:418px;font-size:32px;color:#c3b246">Keep shining!</div>
    {!! $logo('light', 40, 'left:50%;bottom:16px;transform:translateX(-50%);max-width:240px', '#c3b246') !!}

@elseif($c['type'] === 'birthday' && $c['design'] === 2)
    <div style="position:absolute;inset:0;background:#fdf8ec"></div>
    @foreach([[40,40,'#f472b6',-20],[40,120,'#14b8a6',30],[470,50,'#f59e0b',15],[400,120,'#011e48',-35],[60,250,'#c3b246',40],[480,260,'#f472b6',-10],[30,380,'#14b8a6',20],[500,390,'#f59e0b',-40],[310,14,'#c3b246',10],[470,110,'#14b8a6',-25],[150,20,'#011e48',45],[90,160,'#f59e0b',5]] as [$dx,$dy,$col,$r])
        <div class="bit" style="left:{{ $dx }}px;top:{{ $dy }}px;background:{{ $col }};transform:rotate({{ $r }}deg)"></div>
    @endforeach
    <div class="ab" style="left:0;right:0;top:32px;font-size:15px;font-weight:700;letter-spacing:6px;color:#c3b246">HAPPY</div>
    <div class="ab serif" style="left:0;right:0;top:50px;font-size:56px;color:#011e48">Birthday!</div>
    {!! $photo(160, 160, '6px solid #fff', 'left:190px;top:128px;box-shadow:0 6px 18px rgba(1,30,72,.2);') !!}
    <div class="nm" style="top:300px;font-size:{{ $nameSize(28, 440) }}px;color:#011e48">{{ $c['name'] }}</div>
    <div class="ds" style="top:336px;color:#6b778c">{{ $c['designation'] }}</div>
    <div class="ms cel-msg" style="top:362px;color:#1f2a3d">{{ $c['message'] }}</div>
    <div class="ab script" style="left:0;right:0;top:412px;font-size:30px;color:#d4a537">Keep shining!</div>
    <div class="ab" style="left:0;right:0;bottom:0;height:56px;background:#011e48"></div>
    {!! $logo('light', 40, 'left:50%;bottom:8px;transform:translateX(-50%);max-width:240px') !!}

@elseif($c['type'] === 'birthday')
    <div style="position:absolute;inset:0;background:#011e48"></div>
    <div class="ab" style="left:0;right:0;top:0;height:236px;background:linear-gradient(135deg,#dccd72,#b3a13a)"></div>
    <div class="dot" style="width:180px;height:180px;left:-60px;top:-70px;background:rgba(255,255,255,.18)"></div>
    <div class="dot" style="width:120px;height:120px;right:-30px;top:110px;background:rgba(255,255,255,.14)"></div>
    {!! $logo('dark', 40, 'left:50%;top:14px;transform:translateX(-50%);max-width:220px') !!}
    <div class="ab" style="left:0;right:0;top:66px;font-size:36px;font-weight:700;letter-spacing:5px;color:#011e48">HAPPY BIRTHDAY</div>
    <div class="ab" style="left:0;right:0;top:110px;font-size:14px;font-weight:700;color:#011e48">{{ $c['date'] }}</div>
    {!! $photo(170, 170, '7px solid #011e48', 'left:185px;top:146px;') !!}
    <div class="nm" style="top:328px;font-size:{{ $nameSize(28, 440) }}px">{{ $c['name'] }}</div>
    <div class="ds" style="top:364px;color:#c3b246">{{ $c['designation'] }}</div>
    <div class="ms cel-msg" style="top:392px;color:#dfe5f0">{{ $c['message'] }}</div>
    <div class="ab script" style="left:0;right:0;top:446px;font-size:34px;color:#c3b246">Keep shining!</div>

@elseif($c['design'] === 1)
    <div style="position:absolute;inset:0;background:#011e48"></div>
    <div class="dot" style="width:300px;height:300px;right:-120px;bottom:-120px;background:rgba(195,178,70,.15)"></div>
    <div class="ab" style="left:0;right:0;top:28px;font-size:14px;font-weight:700;letter-spacing:5px;color:#c3b246">HAPPY WORK ANNIVERSARY</div>
    {!! $photo(170, 170, '6px solid #c3b246', 'left:60px;top:72px;') !!}
    <div class="ab serif" style="left:250px;width:180px;top:56px;font-size:{{ $y >= 10 ? 120 : 150 }}px;line-height:1;color:#c3b246;text-align:right">{{ $y }}</div>
    <div class="ab serif" style="left:432px;top:72px;font-size:36px;color:#c3b246;text-align:left">{{ $suf }}</div>
    <div class="ab" style="left:250px;width:240px;top:212px;font-size:16px;font-weight:700;letter-spacing:5px">YEAR WITH US</div>
    <div class="nm" style="top:270px;font-size:{{ $nameSize(30, 440) }}px">{{ $c['name'] }}</div>
    <div class="ds" style="top:308px;color:#c9d3e6">{{ $c['designation'] }}</div>
    <div class="ms" style="top:338px;color:#c3b246;font-weight:700">Congratulations on completing {{ $y }} successful {{ $y === 1 ? 'year' : 'years' }}!</div>
    <div class="ms cel-msg" style="top:364px;color:#fff">{{ $c['message'] }}</div>
    {!! $logo('light', 40, 'left:50%;bottom:16px;transform:translateX(-50%);max-width:240px', '#c3b246') !!}

@elseif($c['design'] === 2)
    <div style="position:absolute;inset:0;background:#f7f9fc"></div>
    <div class="ab" style="left:18px;top:18px;right:18px;bottom:18px;border:2px solid #c3b246;border-radius:6px"></div>
    {!! $logo('dark', 38, 'left:50%;top:30px;transform:translateX(-50%);max-width:220px') !!}
    <div class="ab script" style="left:0;right:0;top:78px;font-size:60px;line-height:1;color:#011e48">Thank you</div>
    <div class="ab" style="left:40px;right:40px;top:146px;font-size:15px;font-weight:700;color:#6b778c">for {{ $y }} successful {{ $y === 1 ? 'year' : 'years' }} with us · {{ $y }}{{ $suf }} work anniversary</div>
    {!! $photo(150, 150, '5px solid #c3b246', 'left:195px;top:176px;') !!}
    <div class="nm" style="top:338px;font-size:{{ $nameSize(28, 440) }}px;color:#011e48">{{ $c['name'] }}</div>
    <div class="ds" style="top:372px;color:#b3a13a">{{ $c['designation'] }}</div>
    <div class="ms cel-msg" style="top:400px;color:#1f2a3d">{{ $c['message'] }}</div>
    <div class="co" style="bottom:34px;color:#011e48;font-size:11px">Happy work anniversary</div>

@else
    <div style="position:absolute;inset:0;background:#011e48"></div>
    <div class="ab" style="left:-100px;top:150px;width:800px;height:70px;background:#c3b246;transform:rotate(-12deg)"></div>
    <div class="ab" style="left:-100px;top:232px;width:800px;height:10px;background:rgba(195,178,70,.5);transform:rotate(-12deg)"></div>
    {!! $logo('light', 36, 'left:24px;top:16px;max-width:200px;text-align:left', '#c3b246') !!}
    {!! $photo(160, 160, '6px solid #fff', 'left:70px;top:76px;') !!}
    <div class="dot" style="left:300px;top:80px;width:166px;height:166px;background:#c3b246;border:6px solid #fff"></div>
    <div class="ab serif" style="left:300px;width:166px;top:100px;font-size:{{ $y >= 10 ? 62 : 76 }}px;line-height:1;color:#011e48">{{ $y }}<span style="font-size:26px;vertical-align:top">{{ $suf }}</span></div>
    <div class="ab" style="left:300px;width:166px;top:186px;font-size:11px;font-weight:700;letter-spacing:2px;color:#011e48">ANNIVERSARY</div>
    <div class="ab" style="left:0;right:0;top:288px;font-size:14px;font-weight:700;letter-spacing:5px;color:#c3b246">HAPPY WORK ANNIVERSARY</div>
    <div class="nm" style="top:312px;font-size:{{ $nameSize(30, 440) }}px">{{ $c['name'] }}</div>
    <div class="ds" style="top:350px;color:#c9d3e6">{{ $c['designation'] }}</div>
    <div class="ms" style="top:378px;color:#c3b246;font-weight:700">Congratulations on completing {{ $y }} successful {{ $y === 1 ? 'year' : 'years' }}!</div>
    <div class="ms cel-msg" style="top:404px;color:#fff">{{ $c['message'] }}</div>
@endif
</div>
