<header class="topbar">
    <div class="topbar-brand">
        <span class="brand-mark">SAMS</span>
        <div>
            <strong>Smart Asset Management System</strong>
            <small>Institutional GPS asset tracking portal</small>
        </div>
    </div>

    <div class="topbar-user">
        <div class="user-copy">
            <strong>{{ auth()->user()->name }}</strong>
            <small>{{ auth()->user()->email }}</small>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-outline">Logout</button>
        </form>
    </div>
</header>