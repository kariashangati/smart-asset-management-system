<section class="page-heading">
    <div>
        <p class="page-eyebrow">@yield('portal_label', 'Portal')</p>
        <h1>@yield('page_title', 'Dashboard')</h1>
    </div>

    <nav class="breadcrumb-trail" aria-label="Breadcrumb">
        <a href="@yield('dashboard_url', '#')">@yield('portal_label', 'Portal')</a>
        <span>/</span>
        <span>@yield('page_title', 'Dashboard')</span>
    </nav>
</section>