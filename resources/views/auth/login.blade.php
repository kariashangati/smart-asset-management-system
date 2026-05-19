<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Smart Asset Management System</title>
</head>
<body>
    <h1>Login</h1>

    @if ($errors->any())
        <div>
            <strong>Login failed:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('status'))
        <p>{{ session('status') }}</p>
    @endif

    <form method="POST" action="{{ url('/login') }}">
        @csrf

        <div>
            <label for="email">Email</label><br>
            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autofocus
            >
        </div>

        <br>

        <div>
            <label for="password">Password</label><br>
            <input
                id="password"
                type="password"
                name="password"
                required
            >
        </div>

        <br>

        <div>
            <label>
                <input type="checkbox" name="remember">
                Remember me
            </label>
        </div>

        <br>

        <button type="submit">Login</button>
    </form>
</body>
</html>