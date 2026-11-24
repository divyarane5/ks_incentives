@php
    $u = $byId[$id];
    $kids = $children[$id] ?? [];
    $words = preg_split('/\s+/', trim(preg_replace('/^(mr|ms|mrs|dr)\.?\s+/i', '', $u->name)));
    $initials = strtoupper(substr($words[0] ?? '', 0, 1).substr(count($words) > 1 ? end($words) : '', 0, 1));
    $search = strtolower($u->name.' '.$u->employee_code.' '.optional($u->designation)->name);
    $note = $note ?? null;
@endphp
<li data-search="{{ $search }}">
    @if(count($kids))
        <details {{ $depth < 1 ? 'open' : '' }}>
            <summary>@include('users.tree-card', ['u' => $u, 'initials' => $initials, 'kids' => $kids, 'caret' => true, 'note' => $note])</summary>
            <ul>
                @foreach($kids as $kid)
                    @include('users.tree-node', ['id' => $kid, 'depth' => $depth + 1, 'note' => null])
                @endforeach
            </ul>
        </details>
    @else
        @include('users.tree-card', ['u' => $u, 'initials' => $initials, 'kids' => $kids, 'caret' => false, 'note' => $note])
    @endif
</li>
