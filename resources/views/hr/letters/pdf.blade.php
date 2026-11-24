@php
    $company ??= $u->entity ?: ($u->businessUnit?->name ?? config('app.name'));
    $letterhead ??= null; $ownTo ??= false; $ownSign ??= false;
@endphp
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>{{ $l->subject }}</title>
<style>
    @page { margin: 40px 50px 50px; }
    * { font-family: "DejaVu Sans", sans-serif; }
    body { font-size: 11px; color: #1f2a3d; line-height: 1.55; }
    .head { border-bottom: 3px solid #c3b246; padding-bottom: 10px; margin-bottom: 22px; }
    .co { font-size: 17px; font-weight: bold; color: #011e48; }
    .unit { color: #6b778c; font-size: 10px; }
    .meta { width: 100%; margin-bottom: 18px; }
    .meta td { vertical-align: top; }
    .to b { color: #011e48; }
    .subject { font-weight: bold; color: #011e48; margin: 4px 0 14px; font-size: 12px; text-decoration: underline; }
    p { margin: 0 0 10px; }
    ul { margin: 0 0 10px 0; padding-left: 18px; }
    li { margin-bottom: 4px; }
    .sign { margin-top: 36px; width: 100%; }
    .sign td { vertical-align: bottom; width: 50%; }
    .line { border-top: 1px solid #1f2a3d; width: 180px; padding-top: 4px; margin-top: 44px; }
    .signed { margin-top: 6px; height: 78px; position: relative; }
    .signed .sig { height: 62px; max-width: 210px; margin-top: 10px; }
    .signed .stamp { position: absolute; left: 120px; top: -6px; height: 84px; max-width: 120px; opacity: .9; }
    .who { border-top: 1px solid #1f2a3d; width: 200px; padding-top: 4px; }
    .who b { color: #011e48; }
    .foot { position: fixed; bottom: -30px; left: 0; right: 0; text-align: center; font-size: 8.5px; color: #8a94a6; }
    h1 { text-align: center; font-size: 12.5px; color: #0b4f9c; margin: 0 0 10px; }
    h2 { font-size: 11.5px; color: #1f2a3d; margin: 14px 0 6px; }
    .pb { page-break-after: always; }
    table.lt { width: 78%; margin: 6px auto 14px; border-collapse: collapse; }
    table.lt td { border: 1px solid #1f2a3d; padding: 3px 8px; text-align: center; }
    table.lt td.n { width: 22%; }
    .dsc-space { height: 70px; }
    .keep { page-break-inside: avoid; margin: 6px 0 12px; }
    @if($letterhead)
        @page { margin: 200pt 46pt 122pt; }
        body { font-family: Arial, Helvetica, "DejaVu Sans", sans-serif; text-align: justify; font-size: 10.5pt; line-height: 1.45; }
        h1 { font-size: 11pt; } h2 { font-size: 10.5pt; }
        p { margin: 0 0 9pt; }
        * { font-family: Arial, Helvetica, "DejaVu Sans", sans-serif; }
        .lh-head { position: fixed; top: -200pt; left: -46pt; right: -46pt; height: 198pt; }
        .lh-foot { position: fixed; bottom: -122pt; left: -46pt; right: -46pt; height: 114pt; }
        .lh-head img, .lh-foot img { width: 100%; }
        .subject { text-align: center; text-decoration: none; font-size: 10.5pt; }
    @endif
</style>
</head>
<body>
    @if($letterhead)
        <div class="lh-head"><img src="{{ $letterhead['header'] }}" alt=""></div>
        <div class="lh-foot"><img src="{{ $letterhead['footer'] }}" alt=""></div>
    @else
        <div class="head">
            <div class="co">{{ $company }}</div>
            @if($u->businessUnit?->name && $u->businessUnit->name !== $company)<div class="unit">{{ $u->businessUnit->name }}</div>@endif
        </div>
    @endif

    @unless($ownTo)
        @include('hr.letters._to')
    @endunless

    {!! $html !!}

@unless($ownSign)
    <table class="sign"><tr>
        <td>
            For <b>{{ $company }}</b>
            @if(! empty($signatory) && ($signImg ?? null))
                <div class="signed">
                    <img class="sig" src="{{ $signImg }}" alt="">
                    @if($stampImg ?? null)<img class="stamp" src="{{ $stampImg }}" alt="">@endif
                </div>
                <div class="who">
                    <b>{{ $signatory->name }}</b><br>
                    @if($signatory->designation){{ $signatory->designation }}<br>@endif
                    Authorised signatory
                </div>
            @else
                <div class="line">Authorised signatory</div>
            @endif
        </td>
        <td style="text-align:right">
            @if(in_array($l->type, ['offer', 'appointment'], true))
                <div>I accept the above terms.</div>
                <div class="line" style="margin-left:auto">{{ $u->name }}<br>Date:</div>
            @endif
        </td>
    </tr></table>

@endunless

    @unless($letterhead)<div class="foot">{{ $company }} · {{ $l->reference }}</div>@endunless
</body>
</html>
