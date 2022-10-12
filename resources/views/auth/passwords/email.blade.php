@extends('layouts.auth')

@section('content')
    <!-- Forgot Password -->
    <div class="card">
        <div class="card-body">
            <!-- Logo -->
            <div class="app-brand justify-content-center">
                <img src="{{ asset("assets/img/logo/logo.png") }}">
            </div>
            <!-- /Logo -->
            <h4 class="mb-2">{{ __('Forgot Your Password?') }} 🔒</h4>
            <p class="mb-4">Enter your email and we'll send you mail to reset your password</p>
            <form id="formAuthentication" class="mb-3" action="{{ route('password.email') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label">{{ __('Email Address') }}</label>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary d-grid w-100">Send Reset Link</button>
            </form>
            <div class="text-center">
            <a href="{{ route('login') }}" class="d-flex align-items-center justify-content-center">
                <i class="bx bx-chevron-left scaleX-n1-rtl bx-sm"></i>
                Back to login
            </a>
            </div>
        </div>
    </div>
    <!-- /Forgot Password -->
@endsection
