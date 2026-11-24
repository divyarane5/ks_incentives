@extends('layouts.auth')

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="app-brand justify-content-center">
                <img src="{{ asset('assets/img/logo/realtysuite.svg') }}" class="logo" alt="RealtySuite">
            </div>
            <h4 class="mb-1 text-center">Set a new password</h4>
            <p class="mb-4 text-center text-muted">Enter the 6-digit code we emailed to <b>{{ $maskedEmail }}</b>.</p>

            <form class="mb-3" action="{{ route('password.otp.verify') }}" method="POST" autocomplete="off">
                @csrf
                <div class="mb-3">
                    <label for="code" class="form-label">6-digit code</label>
                    <input id="code" type="text" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" autocomplete="one-time-code"
                        class="form-control text-center fs-4 @error('code') is-invalid @enderror" style="letter-spacing:.5em" name="code" value="{{ old('code') }}" required autofocus>
                    @error('code')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>
                @foreach([['new_password', 'New password'], ['new_password_confirmation', 'Type the new password again']] as [$name, $label])
                    <div class="mb-3 form-password-toggle">
                        <label class="form-label" for="{{ $name }}">{{ $label }}</label>
                        <div class="input-group input-group-merge">
                            <input id="{{ $name }}" type="password" name="{{ $name }}" class="form-control @error($name) is-invalid @enderror" autocomplete="new-password" required>
                            <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                        </div>
                        @error($name)
                            <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>
                @endforeach
                <p class="small text-muted mb-3">At least 8 characters, with a letter and a number.</p>
                <button type="submit" class="btn btn-primary d-grid w-100">Save password and sign in</button>
            </form>

            <form action="{{ route('password.otp.resend') }}" method="POST" class="text-center mb-2">
                @csrf
                <button type="submit" class="btn btn-link p-0">Didn't get it? Send a new code</button>
            </form>
            <div class="text-center">
                <a href="{{ route('login') }}" class="d-flex align-items-center justify-content-center">
                    <i class="bx bx-chevron-left scaleX-n1-rtl bx-sm"></i> Back to sign in
                </a>
            </div>
        </div>
    </div>
@endsection
