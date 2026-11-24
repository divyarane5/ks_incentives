<table class="meta"><tr>
    <td class="to">
        @unless($l->type === 'experience')
            To,<br>
            <b>{{ trim(($u->title ? $u->title.' ' : '').$u->name) }}</b>@if($letterhead)<b>,</b>@endif<br>
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
