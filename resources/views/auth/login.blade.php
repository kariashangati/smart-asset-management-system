@extends('layouts.auth')

@section('title', 'Login')

@section('content')
    <div class="auth-card">
        <div class="auth-card-header">
            <h2>Sign In</h2>
            <p>Access the Smart Asset Management portal.</p>
        </div>

        @if ($errors->any())
            <div class="form-alert">
                <strong>Login failed.</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ url('/login') }}" class="form-stack">
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
                    autocomplete="email"
                >
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                >
            </div>

            <label class="checkbox-row">
                <input type="checkbox" name="remember">
                <span>Remember me</span>
            </label>

            <button type="submit" class="btn btn-primary btn-block">
                Login
            </button>
        </form>
    </div>
@endsection