@extends('layouts.auth')

@section('content')
    <!-- Register -->
    <div class="card">
        <div class="card-body">
        <!-- Logo -->
        <div class="app-brand justify-content-center">
            <img src="{{ asset("assets/img/logo/ks-logos.webp") }}" class="logo">
        </div>
        <!-- /Logo -->
        <h4 class="mb-1 text-center">Sign in to RealtySuite</h4>
        <p class="mb-4 text-center text-muted">Use your employee ID, email or mobile number</p>

        <form id="formAuthentication" class="mb-3" action="{{ route('login') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label">Email, employee ID or mobile</label>
                <input id="email" type="text" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="Email, employee ID or mobile" autofocus>
                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="mb-3 form-password-toggle">
                <div class="d-flex justify-content-between">
                    <label class="form-label" for="password">{{ __('Password') }}</label>
                    <a href="{{ route('password.request') }}">
                    <small>{{ __('Forgot Your Password?') }}</small>
                    </a>
                </div>
                <div class="input-group input-group-merge">
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" required autocomplete="current-password">
                    <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                </div>
                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label" for="remember">
                        {{ __('Remember Me') }}
                    </label>
                </div>
            </div>
            <div class="mb-3">
            <button class="btn btn-primary d-grid w-100" type="submit">{{ __('Login') }}</button>
            </div>
        </form>
        <div class="rs-modules">
            <span>KeyLeads</span><span>KeyBookings</span><span>KeyAccounts</span><span>KeyPeople</span><span>KeyEarn</span>
        </div>
        </div>
    </div>
    <!-- /Register -->
@endsection
