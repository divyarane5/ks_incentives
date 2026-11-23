@extends('layouts.app')

@section('content')
@include('hr.partials.hr-styles')
<div class="container-xxl flex-grow-1 container-p-y kp">
    <div class="hr-head">
        <div><h4>Team leave</h4><div class="sub">Requests from people who report to you</div></div>
        <a href="{{ route('leave.my') }}" class="btn btn-outline-primary"><i class="bx bx-user"></i> My leave</a>
    </div>

    <div class="kp-panel">
        <div class="kp-panel-h"><h2><i class="bx bx-time-five"></i>Waiting for you</h2><span class="kp-note">{{ $pending->count() }}</span></div>
        <div class="kp-panel-b">
            @if($pending->count())
                <ul class="kp-list">
                    @foreach($pending as $r) @include('hr.leave._request', ['r' => $r, 'decide' => true]) @endforeach
                </ul>
            @else
                <div class="kp-empty">Nothing to approve right now.</div>
            @endif
        </div>
    </div>

    <div class="kp-panel">
        <div class="kp-panel-h"><h2><i class="bx bx-plane-take-off"></i>Team away in the next 2 weeks</h2></div>
        <div class="kp-panel-b">
            @if($away->count())
                <ul class="kp-list">@foreach($away as $r) @include('hr.leave._request', ['r' => $r]) @endforeach</ul>
            @else
                <div class="kp-empty">Nobody in your team is on leave in the next 2 weeks.</div>
            @endif
        </div>
    </div>

    @if($recent->count())
        <div class="kp-panel">
            <div class="kp-panel-h"><h2><i class="bx bx-history"></i>Recently decided</h2></div>
            <div class="kp-panel-b"><ul class="kp-list">@foreach($recent as $r) @include('hr.leave._request', ['r' => $r]) @endforeach</ul></div>
        </div>
    @endif
</div>
@endsection
