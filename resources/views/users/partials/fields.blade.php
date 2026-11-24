{{-- $items: list of [label, value] or [label, value, 'html'] (value already escaped). --}}
<dl class="kp-fields {{ $one ?? false ? 'one' : '' }}">
    @foreach($items as $item)
        @php
            [$label, $value] = $item;
            $isHtml = ($item[2] ?? null) === 'html';
            $blank = $value === null || $value === '' || $value === '-';
        @endphp
        <div class="kp-field">
            <dt>{{ $label }}</dt>
            @if($blank)
                <dd class="empty">Not added</dd>
            @elseif($isHtml)
                <dd>{!! $value !!}</dd>
            @else
                <dd>{{ $value }}</dd>
            @endif
        </div>
    @endforeach
</dl>
