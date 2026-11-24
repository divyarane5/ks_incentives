<table class="meta"><tr>
    <td class="to">
        @unless($l->type === 'experience')
            To,<br>
            @php
                $t = trim((string) $u->title);
                $nm = trim((string) $u->name);
                $hasTitle = $t !== '' && stripos(rtrim($nm, '.').' ', rtrim($t, '.')) === 0;
            @endphp
            <b>{{ $t && ! $hasTitle && ! preg_match('/^(mr|ms|mrs|miss|dr)\.?\s/i', $nm) ? $t.' '.$nm : $nm }}</b>@if($letterhead)<b>,</b>@endif<br>
            @if($letterhead)
                @if($u->location?->name)<b>{{ $u->location->name }}.</b>@endif
            @else
                @if($u->designation?->name){{ $u->designation->name }}<br>@endif
                @if($u->communication_address){!! nl2br(e($u->communication_address)) !!}@endif
            @endif
        @endunless
    </td>
    <td style="text-align:right;white-space:nowrap">
        <b>Date: {{ $l->letter_date->format('jS F Y') }}</b><br>
        @if($l->reference)Ref: {{ $l->reference }}@endif
    </td>
</tr></table>
@if($l->subject)<div class="subject">{{ $letterhead ? 'Sub:' : 'Subject:' }} {{ $l->subject }}</div>@endif
