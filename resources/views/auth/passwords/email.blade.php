@extends('layouts.auth')

@section('title', 'Reset Password')

@section('content')
    <div class="auth-card">
        <div class="auth-card-header">
            <h2>Forgot Password?</h2>
            <p>Enter your email to receive a password reset link.</p>
        </div>

        @if (session('status'))
            <div class="form-alert alert-success">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="form-alert alert-danger">
                <strong>Error</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="form-stack">
            @csrf

            <div class="form-group">
                <label for="email">Email Address</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    class="form-control"
                >
                @error('email')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary btn-block">
                Send Password Reset Link
            </button>
        </form>

        <div class="form-footer">
            <p>
                Remember your password? <a href="{{ route('login') }}" class="link-primary">Login here</a>
            </p>
        </div>
    </div>
@endsection
