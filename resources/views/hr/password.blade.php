@extends('layouts.app')

@section('content')
@include('hr.partials.hr-styles')
<style>
    .pw-wrap { max-width: 460px; }
    .pw-warn { background: #fff8e1; border: 1px solid #f2d98a; color: #6b5400; border-radius: 14px; padding: 12px 14px; margin-bottom: 14px; font-size: .9rem; }
    .pw-warn b { display: block; margin-bottom: 2px; }
    .pw-field { position: relative; }
    .pw-field .form-control { padding-right: 46px; }
    .pw-eye { position: absolute; right: 4px; top: 50%; transform: translateY(-50%); border: 0; background: none; color: var(--kp-muted); font-size: 1.25rem; padding: 6px 10px; }
    .pw-tips { font-size: .8rem; color: var(--kp-muted); margin: 0 0 14px; padding-left: 18px; }
    .pw-wrap .btn-primary { min-height: 46px; border-radius: 10px; font-weight: 600; }
</style>
<div class="container-xxl flex-grow-1 container-p-y kp">
    <div class="pw-wrap">
        <div class="hr-head"><div><h4>Change password</h4><div class="sub">Signed in as {{ auth()->user()->name }}{{ auth()->user()->employee_code ? ' ('.auth()->user()->employee_code.')' : '' }}</div></div></div>

        @if($mustChange)
            <div class="pw-warn"><b>Please set your own password</b>You are still using the starting password. Choose a new one to continue using RealtySuite.</div>
        @endif

        <div class="kp-panel"><div class="kp-panel-b">
            <form method="POST" action="{{ route('me.password.update') }}" class="hr-form" autocomplete="off">
                @csrf
                @foreach([
                    ['current_password', 'Current password', 'current-password'],
                    ['new_password', 'New password', 'new-password'],
                    ['new_password_confirmation', 'Type the new password again', 'new-password'],
                ] as [$name, $label, $ac])
                    <div class="mb-3">
                        <label class="form-label" for="{{ $name }}">{{ $label }}</label>
                        <div class="pw-field">
                            <input id="{{ $name }}" type="password" name="{{ $name }}" class="form-control @error($name) is-invalid @enderror" autocomplete="{{ $ac }}" required>
                            <button type="button" class="pw-eye" data-eye="{{ $name }}" aria-label="Show password"><i class="bx bx-hide"></i></button>
                        </div>
                        @error($name)<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                @endforeach
                <ul class="pw-tips">
                    <li>At least 8 characters</li>
                    <li>At least one letter and one number</li>
                    <li>Not the starting password</li>
                </ul>
                <button class="btn btn-primary w-100">Save new password</button>
            </form>
        </div></div>

        <p class="text-muted small mt-3 mb-0">Forgot your password later? Use <b>Forgot password</b> on the sign-in page and we'll email a 6-digit code to your official email.</p>
    </div>
</div>
<script>
    document.querySelectorAll('[data-eye]').forEach(function (b) {
        b.addEventListener('click', function () {
            var i = document.getElementById(b.dataset.eye), show = i.type === 'password';
            i.type = show ? 'text' : 'password';
            b.querySelector('i').className = show ? 'bx bx-show' : 'bx bx-hide';
        });
    });
</script>
@endsection
