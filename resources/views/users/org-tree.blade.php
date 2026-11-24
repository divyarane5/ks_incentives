@extends('layouts.app')

@section('content')
<style>
    .org-tools { display: flex; flex-wrap: wrap; gap: .5rem; align-items: end; }
    .org-tools .form-control { min-width: 0; }
    .org-tree, .org-tree ul { list-style: none; margin: 0; padding: 0; }
    .org-tree ul { margin-left: 1.1rem; padding-left: .9rem; border-left: 2px solid var(--rs-line, #e6eaf2); }
    .org-tree li { margin: .35rem 0; }
    .org-tree details > summary { list-style: none; cursor: pointer; }
    .org-tree details > summary::-webkit-details-marker { display: none; }
    .org-node {
        display: flex; align-items: center; gap: .7rem;
        background: #fff; border: 1px solid var(--rs-line, #e6eaf2); border-radius: 10px;
        padding: .55rem .75rem; max-width: 560px;
    }
    .org-node:hover { border-color: color-mix(in srgb, var(--rs-gold, #c3b246) 60%, #e6eaf2); }
    .org-caret { width: 18px; flex: 0 0 18px; text-align: center; color: var(--rs-muted, #6b778c); transition: transform .15s; }
    details[open] > summary .org-caret { transform: rotate(90deg); }
    .org-avatar {
        width: 34px; height: 34px; flex: 0 0 34px; border-radius: 50%;
        background: var(--rs-navy, #011e48); color: #fff; font-weight: 700; font-size: .8rem;
        display: flex; align-items: center; justify-content: center;
    }
    .org-name { font-weight: 600; color: var(--rs-text, #1f2a3d); line-height: 1.2; }
    .org-meta { font-size: .78rem; color: var(--rs-muted, #6b778c); }
    .org-count { margin-left: auto; white-space: nowrap; font-size: .75rem; font-weight: 600;
        color: var(--rs-navy, #011e48); background: color-mix(in srgb, var(--rs-gold, #c3b246) 18%, #fff);
        border-radius: 999px; padding: .15rem .55rem; }
    .org-link { color: var(--rs-muted, #6b778c); font-size: 1.1rem; }
    .org-hit > summary .org-node, .org-hit > .org-node { border-color: var(--rs-gold, #c3b246); box-shadow: 0 0 0 3px color-mix(in srgb, var(--rs-gold, #c3b246) 25%, transparent); }
    .org-hide { display: none !important; }
    @media (max-width: 575.98px) {
        .org-tree ul { margin-left: .45rem; padding-left: .55rem; }
        .org-node { padding: .5rem .6rem; gap: .5rem; }
        .org-avatar { width: 28px; height: 28px; flex-basis: 28px; font-size: .7rem; }
    }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <h4 class="fw-bold py-3 mb-2 col-md-6"><span class="text-muted fw-light">KeyPeople /</span> Org chart</h4>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ url('/org_tree') }}" class="org-tools">
                <div>
                    <label class="form-label" for="date">As on</label>
                    <input type="date" id="date" name="date" value="{{ $date }}" max="{{ now()->toDateString() }}"
                           class="form-control" onchange="this.form.submit()">
                </div>
                <div class="flex-grow-1" style="min-width: 200px">
                    <label class="form-label" for="org-search">Find a person</label>
                    <input type="search" id="org-search" class="form-control" placeholder="Name, employee ID or designation">
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary" onclick="orgAll(true)">Expand all</button>
                    <button type="button" class="btn btn-outline-secondary" onclick="orgAll(false)">Collapse</button>
                    @unless($isToday)
                        <a href="{{ url('/org_tree') }}" class="btn btn-outline-primary">Today</a>
                    @endunless
                </div>
            </form>
            <div class="small text-muted mt-2">
                {{ number_format($headcount) }} people {{ $isToday ? 'today' : 'on '.\Carbon\Carbon::parse($date)->format('d M Y') }}.
                @unless($isToday)
                    Managers come from the reporting manager history; where none was recorded, the current manager is used.
                @endunless
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <ul class="org-tree" id="org-tree">
                @foreach($roots as $id)
                    @include('users.tree-node', ['id' => $id, 'depth' => 0])
                @endforeach
            </ul>
            @if(empty($roots))
                <div class="text-muted">Nobody found for this date.</div>
            @endif
        </div>
    </div>

    @if(count($orphans))
        <div class="card mb-3">
            <div class="card-header">Manager not with us {{ $isToday ? 'any more' : 'on this date' }} ({{ count($orphans) }})</div>
            <div class="card-body">
                <p class="small text-muted mb-2">These people report to someone who has left (or the link is missing). Update their manager in Edit User.</p>
                <ul class="org-tree">
                    @foreach($orphans as $id)
                        @php($fm = $formerManagers[$managerOf[$id]] ?? null)
                        @include('users.tree-node', ['id' => $id, 'depth' => 0, 'note' => $fm ? 'Reports to '.$fm->name.' ('.$fm->status.')' : 'Manager missing'])
                    @endforeach
                </ul>
            </div>
        </div>
    @endif
</div>
@endsection

@section('script')
<script>
function orgAll(open) {
    document.querySelectorAll('#org-tree details, .org-tree details').forEach(d => d.open = open);
}
(function () {
    const box = document.getElementById('org-search');
    if (!box) return;
    let t;
    box.addEventListener('input', function () {
        clearTimeout(t);
        t = setTimeout(() => {
            const q = box.value.trim().toLowerCase();
            const items = document.querySelectorAll('.org-tree li');
            items.forEach(li => { li.classList.remove('org-hide'); li.querySelector(':scope > details, :scope > .org-node')?.parentElement.classList.remove('org-hit'); li.classList.remove('org-hit'); });
            if (!q) return;
            // Hide everything, then show matches with the path above them.
            items.forEach(li => li.classList.add('org-hide'));
            items.forEach(li => {
                if ((li.dataset.search || '').includes(q)) {
                    li.classList.add('org-hit');
                    let el = li;
                    while (el && el.matches && !el.classList.contains('org-tree')) {
                        if (el.tagName === 'LI') el.classList.remove('org-hide');
                        if (el.tagName === 'DETAILS' && el !== li.firstElementChild) el.open = true;
                        el = el.parentElement;
                    }
                }
            });
        }, 200);
    });
})();
</script>
@endsection
