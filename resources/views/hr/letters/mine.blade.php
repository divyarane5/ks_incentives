@extends('layouts.app')

@section('content')
@include('hr.partials.hr-styles')
<div class="container-xxl flex-grow-1 container-p-y kp">
    <div class="hr-head"><div><h4>My letters</h4><div class="sub">Letters HR has issued to you.</div></div></div>
    <div class="kp-panel"><div class="kp-panel-b">
        @forelse($letters as $l)
            <ul class="kp-list"><li>
                <div class="ic"><i class="bx bx-file"></i></div>
                <div class="bd"><div class="t">{{ $l->subject ?: ucfirst($l->type).' letter' }}</div><div class="m">{{ $l->letter_date->format('d M Y') }} · {{ $l->reference }}</div></div>
                <div class="r d-flex gap-1">
                    <a class="btn btn-sm btn-outline-primary" target="_blank" href="{{ route('letters.pdf', $l) }}">Open</a>
                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('letters.pdf', $l) }}?download=1"><i class="bx bx-download"></i></a>
                </div>
            </li></ul>
        @empty
            <div class="kp-empty">No letters yet.</div>
        @endforelse
    </div></div>
</div>
@endsection
