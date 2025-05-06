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
        <h4 class="mb-2">Welcome to Keystone Real Estate Advisory 👋</h4>
        <p class="mb-4">Please login to your account</p>

        <form id="formAuthentication" class="mb-3" action="{{ route('login') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label">{{ __('Email Address') }}</label>
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="Enter your email or username" autofocus>
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
        </div>
    </div>
    <!-- /Register -->
@endsection
