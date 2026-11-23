@php $company = $u->entity ?: ($u->businessUnit?->name ?? config('app.name')); @endphp
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
    .foot { position: fixed; bottom: -30px; left: 0; right: 0; text-align: center; font-size: 8.5px; color: #8a94a6; }
</style>
</head>
<body>
    <div class="head">
        <div class="co">{{ $company }}</div>
        @if($u->businessUnit?->name && $u->businessUnit->name !== $company)<div class="unit">{{ $u->businessUnit->name }}</div>@endif
    </div>

    <table class="meta"><tr>
        <td class="to">
            @unless($l->type === 'experience')
                To,<br>
                <b>{{ trim(($u->title ? $u->title.' ' : '').$u->name) }}</b><br>
                @if($u->designation?->name){{ $u->designation->name }}<br>@endif
                @if($u->communication_address){!! nl2br(e($u->communication_address)) !!}@endif
            @endunless
        </td>
        <td style="text-align:right;white-space:nowrap">
            Date: {{ $l->letter_date->format('d F Y') }}<br>
            @if($l->reference)Ref: {{ $l->reference }}@endif
        </td>
    </tr></table>

    @if($l->subject)<div class="subject">Subject: {{ $l->subject }}</div>@endif

    {!! $html !!}

    <table class="sign"><tr>
        <td>
            For <b>{{ $company }}</b>
            <div class="line">Authorised signatory</div>
        </td>
        <td style="text-align:right">
            @if(in_array($l->type, ['offer', 'appointment'], true))
                <div>I accept the above terms.</div>
                <div class="line" style="margin-left:auto">{{ $u->name }}<br>Date:</div>
            @endif
        </td>
    </tr></table>

    <div class="foot">{{ $company }} · {{ $l->reference }}</div>
</body>
</html>
