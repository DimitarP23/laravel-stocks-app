@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">Login</div>

            <div class="card-body">
                <form method="POST" action="{{ route('login') }}" autocomplete="on">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                               name="email" value="{{ old('email') }}" required
                               autocomplete="username" autofocus>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                               name="password" required autocomplete="current-password">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember">
                            <label class="form-check-label" for="remember">
                                Remember Me
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary">Login</button>
                    <strong>Forgot Your Password(temporary unavailable)?</strong>
                    </div>

                    <div class="alert alert-info">
                        <i class="bi bi-shield-lock"></i>
                        <strong>Password Manager Tip:</strong> Using a password manager? Great choice!
                        This form supports auto-fill from password managers for enhanced security.
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
