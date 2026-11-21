<div class="org-node">
    <span class="org-caret">@if($caret)<i class="bx bx-chevron-right"></i>@endif</span>
    <span class="org-avatar">{{ $initials ?: '?' }}</span>
    <span class="min-w-0">
        <span class="org-name d-block">{{ $u->name }}</span>
        <span class="org-meta d-block">{{ $u->employee_code }}{{ optional($u->designation)->name ? ' · '.$u->designation->name : '' }}</span>
        @if(!empty($note))<span class="org-meta d-block text-warning">{{ $note }}</span>@endif
    </span>
    @if(count($kids))
        <span class="org-count" title="{{ count($kids) }} direct, {{ $total[$u->id] ?? 0 }} in total">👥 {{ count($kids) }}@if(($total[$u->id] ?? 0) > count($kids)) · {{ $total[$u->id] }}@endif</span>
    @endif
    <a class="org-link {{ count($kids) ? '' : 'ms-auto' }}" href="{{ route('users.show', $u->id) }}" title="Open profile" onclick="event.stopPropagation()"><i class="bx bx-user-circle"></i></a>
</div>
