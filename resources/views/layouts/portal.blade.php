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
    <link rel="icon" href="{{ asset('assets/img/cccc.png') }}" type="image/x-icon">
</head>
<body @class(['portal-body', 'landing-loader-active' => request()->routeIs('home', 'login')])>
    <header @class(['portal-topbar', 'portal-topbar-landing' => request()->routeIs('home', 'login')])>
        @if (request()->routeIs('home', 'login'))
            <span class="portal-topbar-curve portal-topbar-curve-left" aria-hidden="true"></span>
            <span class="portal-topbar-curve portal-topbar-curve-right" aria-hidden="true"></span>
        @endif
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

    @auth
        @if (! request()->routeIs('home', 'login'))
            <div class="authenticated-skeleton" role="status" aria-label="Loading page">
                <div class="authenticated-skeleton-page">
                    @if (request()->routeIs('admin.dashboard', 'registrar.dashboard'))
                        <div class="skeleton-heading">
                            <div>
                                <span class="skeleton-line skeleton-kicker"></span>
                                <span class="skeleton-line skeleton-title"></span>
                                <span class="skeleton-line skeleton-subtitle"></span>
                            </div>
                            <span class="skeleton-block skeleton-action"></span>
                        </div>
                        <div class="skeleton-metrics">
                            <span></span><span></span><span></span><span></span>
                        </div>
                        <div class="skeleton-dashboard-grid">
                            <span class="skeleton-chart"></span>
                            <span class="skeleton-chart skeleton-chart-small"></span>
                        </div>
                        <div class="skeleton-dashboard-grid skeleton-dashboard-grid-bottom">
                            <span class="skeleton-table"></span>
                            <span class="skeleton-table skeleton-table-small"></span>
                        </div>
                    @elseif (request()->routeIs('students.*'))
                        <div class="skeleton-record-heading">
                            <div>
                                <span class="skeleton-line skeleton-kicker"></span>
                                <span class="skeleton-line skeleton-title"></span>
                            </div>
                            <span class="skeleton-block skeleton-record-count"></span>
                        </div>
                        <div class="skeleton-record-layout">
                            <div class="skeleton-record-list">
                                <span></span><span></span><span></span><span></span>
                            </div>
                            <div class="skeleton-record-form">
                                <span class="skeleton-line skeleton-subtitle"></span>
                                <span></span><span></span><span></span><span></span>
                            </div>
                        </div>
                    @else
                        <div class="skeleton-heading">
                            <span class="skeleton-line skeleton-title"></span>
                            <span class="skeleton-block skeleton-action"></span>
                        </div>
                        <div class="skeleton-generic-grid">
                            <span></span><span></span><span></span>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    @endauth

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

        document.addEventListener('click', (event) => {
            const button = document.querySelector('.portal-menu-toggle');
            const menu = document.querySelector('#portal-mobile-menu');
            if (!button || !menu || button.getAttribute('aria-expanded') !== 'true') return;
            if (button.contains(event.target) || menu.contains(event.target)) return;

            button.setAttribute('aria-expanded', 'false');
            button.setAttribute('aria-label', 'Open navigation menu');
            menu.classList.remove('is-open');
        });

        window.addEventListener('resize', () => {
            if (window.innerWidth > 580) {
                const button = document.querySelector('.portal-menu-toggle');
                const menu = document.querySelector('#portal-mobile-menu');
                if (!button || !menu) return;
                button.setAttribute('aria-expanded', 'false');
                button.setAttribute('aria-label', 'Open navigation menu');
                menu.classList.remove('is-open');
            }
        });

        const programDeck = document.querySelector('.landing-program-deck');
        if (programDeck) {
            const deckCards = [...programDeck.querySelectorAll('.landing-program-card')];
            let swipe = null;
            let suppressDeckClick = false;

            const bringCardToFront = (selectedCard) => {
                const selectedIndex = deckCards.indexOf(selectedCard);
                deckCards.forEach((card, index) => {
                    let position = index - selectedIndex;
                    if (position < -2) position += deckCards.length;
                    if (position > 2) position -= deckCards.length;

                    card.dataset.deckPosition = String(position);
                    card.classList.toggle('is-front', index === selectedIndex);
                    card.setAttribute('aria-pressed', String(index === selectedIndex));
                });
            };

            deckCards.forEach((card) => {
                card.addEventListener('click', () => {
                    if (!suppressDeckClick) bringCardToFront(card);
                    suppressDeckClick = false;
                });
                card.addEventListener('keydown', (event) => {
                    if (event.key !== 'ArrowLeft' && event.key !== 'ArrowRight') return;
                    event.preventDefault();
                    const currentIndex = deckCards.indexOf(card);
                    const step = event.key === 'ArrowRight' ? 1 : -1;
                    const nextIndex = (currentIndex + step + deckCards.length) % deckCards.length;
                    deckCards[nextIndex].focus();
                    bringCardToFront(deckCards[nextIndex]);
                });

                card.addEventListener('pointerdown', (event) => {
                    if (event.pointerType === 'mouse' && event.button !== 0) return;
                    if (!card.classList.contains('is-front')) {
                        bringCardToFront(card);
                    }
                    swipe = {
                        card,
                        pointerId: event.pointerId,
                        startX: event.clientX,
                        startY: event.clientY,
                        dx: 0,
                        dy: 0,
                    };
                    card.classList.add('is-dragging');
                    card.setPointerCapture?.(event.pointerId);
                });

                card.addEventListener('pointermove', (event) => {
                    if (!swipe || swipe.pointerId !== event.pointerId || swipe.card !== card) return;
                    swipe.dx = event.clientX - swipe.startX;
                    swipe.dy = event.clientY - swipe.startY;

                    if (Math.abs(swipe.dx) < 8 && Math.abs(swipe.dy) < 8) return;
                    const directionX = swipe.dx;
                    const directionY = swipe.dy;
                    card.style.setProperty('--deck-x', `${directionX * .65}px`);
                    card.style.setProperty('--deck-y', `${directionY * .35}px`);
                    card.style.setProperty('--deck-rotate', `${directionX * .035}deg`);
                    card.style.setProperty('--deck-scale', '.96');
                    card.style.opacity = String(Math.max(.68, 1 - Math.abs(directionX) / 500));
                });

                const finishSwipe = (event) => {
                    if (!swipe || swipe.pointerId !== event.pointerId || swipe.card !== card) return;
                    card.releasePointerCapture?.(event.pointerId);

                    const { dx, dy } = swipe;
                    swipe = null;
                    card.style.removeProperty('--deck-x');
                    card.style.removeProperty('--deck-y');
                    card.style.removeProperty('--deck-rotate');
                    card.style.removeProperty('--deck-scale');
                    card.style.opacity = '';
                    card.classList.remove('is-dragging');

                    const isSwipe = Math.max(Math.abs(dx), Math.abs(dy)) >= 48;
                    if (!isSwipe) return;

                    suppressDeckClick = true;
                    const currentIndex = deckCards.indexOf(card);
                    const horizontal = Math.abs(dx) >= Math.abs(dy);
                    const direction = horizontal
                        ? (dx < 0 ? -1 : 1)
                        : (dy < 0 ? -1 : 1);

                    card.classList.add(direction < 0 ? 'is-swiping-left' : 'is-swiping-right');
                    window.setTimeout(() => {
                        card.classList.remove('is-swiping-left', 'is-swiping-right');
                        const nextIndex = (currentIndex + direction + deckCards.length) % deckCards.length;
                        bringCardToFront(deckCards[nextIndex]);
                    }, 320);
                };

                card.addEventListener('pointerup', finishSwipe);
                card.addEventListener('pointercancel', finishSwipe);
            });
        }

        const authenticatedSkeleton = document.querySelector('.authenticated-skeleton');
        if (authenticatedSkeleton) {
            const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
            const exitAuthenticatedSkeleton = () => {
                authenticatedSkeleton.classList.add('is-exiting');
                window.setTimeout(() => authenticatedSkeleton.remove(), reduceMotion.matches ? 20 : 420);
            };

            if (document.readyState === 'complete') {
                window.setTimeout(exitAuthenticatedSkeleton, reduceMotion.matches ? 20 : 620);
            } else {
                window.addEventListener('load', () => {
                    window.setTimeout(exitAuthenticatedSkeleton, reduceMotion.matches ? 20 : 620);
                }, { once: true });
            }
        }

        const landingLoader = document.querySelector('.landing-loader');
        if (landingLoader) {
            const loaderLogo = landingLoader.querySelector('.landing-loader-logo');
            const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

            const finishLandingLoader = () => {
                const headerLogo = document.querySelector('.portal-topbar-landing .college-logo');
                if (headerLogo && loaderLogo) {
                    const loaderRect = loaderLogo.getBoundingClientRect();
                    const headerRect = headerLogo.getBoundingClientRect();
                    const deltaX = (headerRect.left + headerRect.width / 2) - (loaderRect.left + loaderRect.width / 2);
                    const deltaY = (headerRect.top + headerRect.height / 2) - (loaderRect.top + loaderRect.height / 2);

                    landingLoader.style.setProperty('--loader-x', `${deltaX}px`);
                    landingLoader.style.setProperty('--loader-y', `${deltaY}px`);
                }

                landingLoader.classList.add('is-exiting');
                window.setTimeout(() => {
                    document.body.classList.remove('landing-loader-active');
                    landingLoader.remove();
                }, reduceMotion.matches ? 20 : 2300);
            };

            const startLandingLoader = () => {
                window.setTimeout(finishLandingLoader, reduceMotion.matches ? 120 : 4500);
            };

            if (document.readyState === 'complete') {
                startLandingLoader();
            } else {
                window.addEventListener('load', startLandingLoader, { once: true });
            }
        }

        const scrollRevealShell = document.querySelector('.scroll-reveal-shell');
        if (scrollRevealShell) {
            const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
            let revealFrame = 0;

            if (window.scrollY > 0) {
                scrollRevealShell.classList.remove('is-scroll-reveal-pending');
            }

            const updateScrollReveal = () => {
                revealFrame = 0;
                if (reduceMotion.matches) return;

                const rect = scrollRevealShell.getBoundingClientRect();
                const range = Math.max(scrollRevealShell.offsetHeight - window.innerHeight * .45, 1);
                const progress = Math.min(1, Math.max(0, (window.innerHeight * .82 - rect.top) / range));
                const eased = 1 - Math.pow(1 - progress, 3);
                const isMobile = window.innerWidth <= 760;

                scrollRevealShell.style.setProperty('--reveal-frame-y', `${80 * (1 - eased)}px`);
                scrollRevealShell.style.setProperty('--reveal-frame-scale', isMobile ? (.82 + .18 * eased).toFixed(3) : (.78 + .22 * eased).toFixed(3));
                scrollRevealShell.style.setProperty('--reveal-frame-saturate', (.78 + .22 * eased).toFixed(3));
                scrollRevealShell.style.setProperty('--reveal-heading-y', `${-34 * eased}px`);
                scrollRevealShell.style.setProperty('--reveal-heading-opacity', (.45 + .55 * eased).toFixed(3));
            };

            const requestRevealUpdate = () => {
                if (window.scrollY > 0) {
                    scrollRevealShell.classList.remove('is-scroll-reveal-pending');
                }

                if (!revealFrame) revealFrame = window.requestAnimationFrame(updateScrollReveal);
            };

            window.addEventListener('scroll', requestRevealUpdate, { passive: true });
            window.addEventListener('resize', requestRevealUpdate);
            updateScrollReveal();
        }

        const courseScrollShell = document.querySelector('.course-scroll-shell');
        if (courseScrollShell) {
            const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
            let courseScrollFrame = 0;

            const updateCourseScroll = () => {
                courseScrollFrame = 0;
                if (reduceMotion.matches) return;

                const rect = courseScrollShell.getBoundingClientRect();
                const range = Math.max(courseScrollShell.offsetHeight - window.innerHeight * 0.25, 1);
                const progress = Math.min(1, Math.max(0, (window.innerHeight * 0.78 - rect.top) / range));
                const isMobile = window.innerWidth <= 760;
                const rotate = 20 * (1 - progress);
                const scale = isMobile ? 0.7 + (0.2 * progress) : 1.05 - (0.05 * progress);
                const translate = -100 * progress;

                courseScrollShell.style.setProperty('--course-card-rotate', `${rotate}deg`);
                courseScrollShell.style.setProperty('--course-card-scale', scale.toFixed(3));
                courseScrollShell.style.setProperty('--course-header-translate', `${translate}px`);
            };

            const requestCourseScrollUpdate = () => {
                if (!courseScrollFrame) {
                    courseScrollFrame = window.requestAnimationFrame(updateCourseScroll);
                }
            };

            window.addEventListener('scroll', requestCourseScrollUpdate, { passive: true });
            window.addEventListener('resize', requestCourseScrollUpdate);
            updateCourseScroll();
        }
    </script>
</body>
</html>
