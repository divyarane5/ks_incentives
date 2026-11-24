@extends('layouts.auth')

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="app-brand justify-content-center">
                <img src="{{ asset('assets/img/logo/realtysuite.svg') }}" class="logo" alt="RealtySuite">
            </div>
            <h4 class="mb-1 text-center">Forgot your password?</h4>
            <p class="mb-4 text-center text-muted">We'll email a 6-digit code to your official email address.</p>

            <form class="mb-3" action="{{ route('password.email') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="login" class="form-label">Employee ID, email or mobile</label>
                    <input id="login" type="text" class="form-control @error('login') is-invalid @enderror" name="login" value="{{ old('login') }}" required autocomplete="username" placeholder="e.g. KRE001" autofocus>
                    @error('login')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary d-grid w-100">Send code</button>
            </form>
            <div class="text-center">
                <a href="{{ route('login') }}" class="d-flex align-items-center justify-content-center">
                    <i class="bx bx-chevron-left scaleX-n1-rtl bx-sm"></i> Back to sign in
                </a>
            </div>
        </div>
    </div>
@endsection
