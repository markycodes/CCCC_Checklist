<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#a94720">
    <meta name="description" content="@yield('description', 'Catbalogan City Community College student course checklist.')">
    <title>@yield('title') · CCCC Student Checklist</title>
    <link rel="stylesheet" href="{{ asset('css/student-checklist.css') }}">
    <link rel="stylesheet" href="{{ asset('css/portal.css') }}">
    <link rel="icon" href="{{ asset('assets/img/CCCClogo.webp') }}" type="image/x-icon">
</head>
<body class="portal-body">
    <header class="portal-topbar">
        <a class="portal-brand" href="{{ route('home') }}" aria-label="Catbalogan City Community College home">
            <img class="college-logo" src="{{ asset('assets/img/CCCClogo.webp') }}" width="45" height="47" alt="">
            <span class="portal-brand-copy">
                <strong>CATBALOGAN CITY</strong>
                <span>COMMUNITY COLLEGE <i>·</i> STUDENT CHECKLIST</span>
            </span>
        </a>
        <button class="portal-menu-toggle" type="button" aria-label="Open navigation menu" aria-expanded="false" aria-controls="portal-mobile-menu">
            <span></span><span></span><span></span>
        </button>
        <nav id="portal-mobile-menu" class="portal-nav" aria-label="Main navigation">
            @auth
                @if (auth()->user()->isAdministrator())
                    <a class="portal-nav-link {{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}" href="{{ route('admin.dashboard') }}" @if(request()->routeIs('admin.dashboard')) aria-current="page" @endif>Administration</a>
                    <a class="portal-nav-link {{ request()->routeIs('students.*') ? 'is-active' : '' }}" href="{{ route('students.index') }}" @if(request()->routeIs('students.*')) aria-current="page" @endif>All student records</a>
                @elseif (auth()->user()->isRegistrar())
                    <a class="portal-nav-link {{ request()->routeIs('registrar.dashboard') ? 'is-active' : '' }}" href="{{ route('registrar.dashboard') }}" @if(request()->routeIs('registrar.dashboard')) aria-current="page" @endif>Registrar overview</a>
                    <a class="portal-nav-link {{ request()->routeIs('students.*') ? 'is-active' : '' }}" href="{{ route('students.index') }}" @if(request()->routeIs('students.*')) aria-current="page" @endif>Student records</a>
                @else
                    <a class="portal-nav-link {{ request()->routeIs('students.*') ? 'is-active' : '' }}" href="{{ route('students.index') }}" @if(request()->routeIs('students.*')) aria-current="page" @endif>Student records</a>
                @endif
                <span class="portal-user-name">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="portal-nav-button portal-signout" type="submit"><span aria-hidden="true">↪</span> Sign out</button>
                </form>
            @elseif (request()->routeIs('home'))
                <a class="portal-nav-link" href="#curriculum">Course overview</a>
                <a class="portal-nav-button" href="{{ route('login') }}"><span aria-hidden="true">↗</span> Sign in</a>
            @else
                <a class="portal-nav-link" href="{{ route('home') }}">← Back to home</a>
            @endif
        </nav>
    </header>

    @yield('content')

    <footer class="portal-footer portal-footer-design">
        <div class="portal-footer-main">
            <div class="portal-footer-brand">
                <img src="{{ asset('assets/img/CCCClogo.webp') }}" width="58" height="61" alt="Catbalogan City Community College">
                <div>
                    <strong>Catbalogan City Community College</strong>
                    <span>Student Records and Academic Checklist</span>
                </div>
            </div>
            <nav class="portal-footer-links" aria-label="Footer navigation">
                <a href="{{ route('home') }}">Home</a>
                @auth
                    @if(auth()->user()->isAdministrator())
                        <a href="{{ route('admin.dashboard') }}">Administration</a>
                    @elseif(auth()->user()->isRegistrar())
                        <a href="{{ route('registrar.dashboard') }}">Registrar overview</a>
                    @endif
                    <a href="{{ route('students.index') }}">Student records</a>
                @else
                    <a href="{{ route('login') }}">Sign in</a>
                @endauth
            </nav>
            <div class="portal-footer-partners" aria-label="Partner organizations">
                <span>Affiliations & Partners</span>
                <div>
                    <img src="{{ asset('assets/img/partner-catbalogan.png') }}" alt="City of Catbalogan">
                    <img src="{{ asset('assets/img/partner-ched.png') }}" alt="Commission on Higher Education">
                    <img src="{{ asset('assets/img/partner-bagong-pilipinas.png') }}" alt="Bagong Pilipinas">
                </div>
            </div>
        </div>
        <div class="portal-footer-bottom">
            <span>© {{ now()->year }} Catbalogan City Community College</span>
            <span>Academic records portal</span>
        </div>
    </footer>
    <script>
        document.querySelectorAll('.portal-menu-toggle').forEach((button) => {
            const menu = document.querySelector('#portal-mobile-menu');
            if (!menu) return;

            button.addEventListener('click', () => {
                const open = button.getAttribute('aria-expanded') === 'true';
                button.setAttribute('aria-expanded', String(!open));
                button.setAttribute('aria-label', open ? 'Open navigation menu' : 'Close navigation menu');
                menu.classList.toggle('is-open', !open);
            });

            menu.querySelectorAll('a').forEach((link) => {
                link.addEventListener('click', () => {
                    button.setAttribute('aria-expanded', 'false');
                    button.setAttribute('aria-label', 'Open navigation menu');
                    menu.classList.remove('is-open');
                });
            });
        });

        document.addEventListener('keydown', (event) => {
            if (event.key !== 'Escape') return;
            const button = document.querySelector('.portal-menu-toggle');
            const menu = document.querySelector('#portal-mobile-menu');
            if (!button || !menu) return;
            button.setAttribute('aria-expanded', 'false');
            button.setAttribute('aria-label', 'Open navigation menu');
            menu.classList.remove('is-open');
            button.focus();
        });
    </script>
</body>
</html>
