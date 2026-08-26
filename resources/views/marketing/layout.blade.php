@php
    $page = $page ?? 'home';
    $title = match ($page) {
        'product' => __('marketing.meta_product'),
        'faq' => __('marketing.meta_faq'),
        'contact' => __('marketing.meta_contact'),
        default => __('marketing.meta_title'),
    };
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <meta name="description" content="{{ __('marketing.meta_description') }}">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:title" content="{{ $title }}">
    <meta property="og:description" content="{{ __('marketing.meta_description') }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Sora:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/marketing.css') }}">
    <script>
        (function () {
            try {
                var t = localStorage.getItem('workom-theme');
                if (t !== 'light' && t !== 'dark') t = 'dark';
                document.documentElement.setAttribute('data-wo-theme', t);
            } catch (e) {}
        })();
    </script>
    @stack('head')
</head>
<body class="wo" data-theme="dark">
    <script>
        (function () {
            try {
                var t = localStorage.getItem('workom-theme') || 'dark';
                document.body.setAttribute('data-theme', t === 'light' ? 'light' : 'dark');
            } catch (e) {
                document.body.setAttribute('data-theme', 'dark');
            }
        })();
    </script>
    <a class="wo-skip" href="#main">Skip</a>

    <header class="wo-top">
        <div class="wo-wrap wo-top-row">
            <a href="{{ route('home') }}" class="wo-logo">
                <img src="{{ asset('images/marketing/workom_logo.png') }}" alt="WorkOM" width="160" height="160">
            </a>

            <nav class="wo-tabs" aria-label="Primary">
                <a href="{{ route('home') }}" class="{{ $page === 'home' ? 'is-active' : '' }}">{{ __('marketing.nav_home') }}</a>
                <a href="{{ route('marketing.product') }}" class="{{ $page === 'product' ? 'is-active' : '' }}">{{ __('marketing.nav_product') }}</a>
                <a href="{{ route('marketing.faq') }}" class="{{ $page === 'faq' ? 'is-active' : '' }}">{{ __('marketing.nav_faq') }}</a>
                <a href="{{ route('marketing.contact') }}" class="{{ $page === 'contact' ? 'is-active' : '' }}">{{ __('marketing.nav_contact') }}</a>
            </nav>

            <div class="wo-actions">
                <div class="wo-lang" role="group" aria-label="Language">
                    <a href="{{ route('marketing.locale', 'sq') }}" class="{{ app()->getLocale() === 'sq' ? 'is-active' : '' }}">SQ</a>
                    <a href="{{ route('marketing.locale', 'en') }}" class="{{ app()->getLocale() === 'en' ? 'is-active' : '' }}">EN</a>
                </div>
                <button type="button" class="wo-theme" id="woTheme" aria-label="{{ __('marketing.theme_toggle') }}" title="{{ __('marketing.theme_toggle') }}">
                    <span class="wo-theme-icon-dark" aria-hidden="true">☀</span>
                    <span class="wo-theme-icon-light" aria-hidden="true">☾</span>
                </button>
                <a class="wo-btn wo-btn-ghost wo-hide-sm" href="{{ route('login') }}">{{ __('marketing.nav_login') }}</a>
                <a class="wo-btn wo-btn-primary wo-hide-sm" href="{{ route('marketing.contact') }}">{{ __('marketing.nav_demo') }}</a>
                <button type="button" class="wo-menu" id="woMenu" aria-expanded="false" aria-controls="woDrawer" aria-label="{{ __('marketing.nav_menu') }}">
                    <span class="wo-menu-bars" aria-hidden="true">
                        <span></span><span></span><span></span>
                    </span>
                </button>
            </div>
        </div>
        <nav class="wo-drawer" id="woDrawer" aria-label="Mobile">
            <div class="wo-wrap wo-drawer-inner">
                <a href="{{ route('home') }}" class="{{ $page === 'home' ? 'is-active' : '' }}">{{ __('marketing.nav_home') }}</a>
                <a href="{{ route('marketing.product') }}" class="{{ $page === 'product' ? 'is-active' : '' }}">{{ __('marketing.nav_product') }}</a>
                <a href="{{ route('marketing.faq') }}" class="{{ $page === 'faq' ? 'is-active' : '' }}">{{ __('marketing.nav_faq') }}</a>
                <a href="{{ route('marketing.contact') }}" class="{{ $page === 'contact' ? 'is-active' : '' }}">{{ __('marketing.nav_contact') }}</a>
                <div class="wo-drawer-cta">
                    <a class="wo-btn wo-btn-ghost" href="{{ route('login') }}">{{ __('marketing.nav_login') }}</a>
                    <a class="wo-btn wo-btn-primary" href="{{ route('marketing.contact') }}">{{ __('marketing.nav_demo') }}</a>
                </div>
            </div>
        </nav>
    </header>

    <main id="main" class="wo-main">
        @yield('content')
    </main>

    <footer class="wo-footer">
        <div class="wo-wrap">
            <div class="wo-footer-row">
                <div class="wo-footer-brand">
                    <a href="{{ route('home') }}" class="wo-logo wo-logo-footer">
                        <img src="{{ asset('images/marketing/workom_logo.png') }}" alt="WorkOM" width="160" height="160">
                    </a>
                    <p>{{ __('marketing.footer_copy') }}</p>
                </div>
                <div class="wo-footer-col">
                    <h4>{{ __('marketing.footer_explore') }}</h4>
                    <a href="{{ route('marketing.product') }}">{{ __('marketing.nav_product') }}</a>
                    <a href="{{ route('marketing.faq') }}">{{ __('marketing.nav_faq') }}</a>
                    <a href="{{ route('marketing.contact') }}">{{ __('marketing.nav_contact') }}</a>
                </div>
                <div class="wo-footer-col">
                    <h4>{{ __('marketing.footer_contact') }}</h4>
                    <a href="https://wa.me/{{ __('marketing.phone_wa') }}" target="_blank" rel="noopener noreferrer">{{ __('marketing.whatsapp') }} · {{ __('marketing.phone_display') }}</a>
                    <a href="{{ route('marketing.contact') }}">{{ __('marketing.nav_contact') }}</a>
                    <a href="{{ route('login') }}">{{ __('marketing.footer_login') }}</a>
                    <a href="{{ route('register') }}">{{ __('marketing.footer_register') }}</a>
                </div>
            </div>
            <div class="wo-copy">&copy; {{ date('Y') }} WorkOM. All rights reserved.</div>
        </div>
    </footer>

    <script>
        (function () {
            var btn = document.getElementById('woMenu');
            var drawer = document.getElementById('woDrawer');
            function setMenu(open) {
                if (!btn || !drawer) return;
                drawer.classList.toggle('is-open', open);
                btn.classList.toggle('is-open', open);
                btn.setAttribute('aria-expanded', open ? 'true' : 'false');
                document.body.classList.toggle('wo-nav-open', open);
            }
            if (btn && drawer) {
                btn.addEventListener('click', function () {
                    setMenu(!drawer.classList.contains('is-open'));
                });
                drawer.querySelectorAll('a').forEach(function (link) {
                    link.addEventListener('click', function () { setMenu(false); });
                });
                document.addEventListener('keydown', function (e) {
                    if (e.key === 'Escape') setMenu(false);
                });
                window.addEventListener('resize', function () {
                    if (window.matchMedia('(min-width: 920px)').matches) setMenu(false);
                });
            }

            var themeBtn = document.getElementById('woTheme');
            var body = document.body;
            function applyTheme(theme) {
                body.setAttribute('data-theme', theme);
                try { localStorage.setItem('workom-theme', theme); } catch (e) {}
            }
            if (themeBtn) {
                themeBtn.addEventListener('click', function () {
                    var next = body.getAttribute('data-theme') === 'light' ? 'dark' : 'light';
                    applyTheme(next);
                });
            }

            var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            var animated = document.querySelectorAll('.wo-animate');
            if (!reduceMotion && 'IntersectionObserver' in window && animated.length) {
                var io = new IntersectionObserver(function (entries) {
                    entries.forEach(function (entry) {
                        if (!entry.isIntersecting) return;
                        entry.target.classList.add('is-inview');
                        io.unobserve(entry.target);
                    });
                }, { threshold: 0.14, rootMargin: '0px 0px -8% 0px' });
                animated.forEach(function (el) { io.observe(el); });
            } else {
                animated.forEach(function (el) { el.classList.add('is-inview'); });
            }

            document.querySelectorAll('.wo-faq details').forEach(function (details) {
                var summary = details.querySelector('summary');
                var panel = details.querySelector('.wo-faq-panel');
                if (!summary || !panel) return;

                details.classList.add('wo-faq-ready');
                if (details.open) details.classList.add('is-open');

                summary.addEventListener('click', function (e) {
                    e.preventDefault();
                    var opening = !details.classList.contains('is-open');

                    if (reduceMotion) {
                        details.open = opening;
                        details.classList.toggle('is-open', opening);
                        return;
                    }

                    if (opening) {
                        details.open = true;
                        requestAnimationFrame(function () {
                            details.classList.add('is-open');
                        });
                    } else {
                        details.classList.remove('is-open');
                        var done = false;
                        var finish = function () {
                            if (done) return;
                            done = true;
                            details.open = false;
                        };
                        panel.addEventListener('transitionend', function onEnd(ev) {
                            if (ev.target !== panel) return;
                            panel.removeEventListener('transitionend', onEnd);
                            finish();
                        });
                        window.setTimeout(finish, 400);
                    }
                });
            });

            (function initPriceSlider() {
                var track = document.getElementById('woPrices');
                if (!track) return;

                var cards = Array.prototype.slice.call(track.querySelectorAll('.wo-price'));
                if (!cards.length) return;

                var mq = window.matchMedia('(max-width: 919px)');
                var index = Math.max(0, cards.findIndex(function (c) { return c.classList.contains('is-featured'); }));
                if (index < 0) index = 0;
                var ticking = false;
                var drag = null;
                var skipClick = false;

                function setActive(i) {
                    index = i;
                    cards.forEach(function (card, n) {
                        card.classList.toggle('is-slide-active', n === i);
                    });
                }

                function goTo(i, smooth) {
                    if (!mq.matches) return;
                    i = Math.max(0, Math.min(cards.length - 1, i));
                    var card = cards[i];
                    var left = card.offsetLeft - (track.clientWidth - card.clientWidth) / 2;
                    track.scrollTo({ left: left, behavior: smooth && !reduceMotion ? 'smooth' : 'auto' });
                    setActive(i);
                }

                function nearest() {
                    var mid = track.scrollLeft + track.clientWidth / 2;
                    var best = 0;
                    var bestDist = Infinity;
                    cards.forEach(function (card, i) {
                        var c = card.offsetLeft + card.clientWidth / 2;
                        var d = Math.abs(c - mid);
                        if (d < bestDist) {
                            bestDist = d;
                            best = i;
                        }
                    });
                    return best;
                }

                function onScroll() {
                    if (!mq.matches || ticking) return;
                    ticking = true;
                    requestAnimationFrame(function () {
                        setActive(nearest());
                        ticking = false;
                    });
                }

                function onPointerDown(e) {
                    if (!mq.matches || e.button === 2) return;
                    if (e.target.closest('a, button')) return;
                    drag = {
                        startX: e.clientX,
                        startScroll: track.scrollLeft,
                        moved: false,
                        pointerId: e.pointerId
                    };
                    track.classList.add('is-dragging');
                    if (e.pointerId != null && track.setPointerCapture) {
                        try { track.setPointerCapture(e.pointerId); } catch (err) {}
                    }
                }

                function onPointerMove(e) {
                    if (!drag) return;
                    var dx = e.clientX - drag.startX;
                    if (Math.abs(dx) > 6) drag.moved = true;
                    track.scrollLeft = drag.startScroll - dx;
                    if (drag.moved && e.cancelable) e.preventDefault();
                }

                function onPointerUp(e) {
                    if (!drag) return;
                    var dx = e.clientX - drag.startX;
                    var moved = drag.moved;
                    track.classList.remove('is-dragging');
                    if (drag.pointerId != null && track.releasePointerCapture) {
                        try { track.releasePointerCapture(drag.pointerId); } catch (err) {}
                    }
                    drag = null;
                    if (!moved) return;
                    skipClick = true;
                    var next = index;
                    if (dx < -48) next = index + 1;
                    else if (dx > 48) next = index - 1;
                    else next = nearest();
                    goTo(next, true);
                }

                track.addEventListener('scroll', onScroll, { passive: true });
                track.addEventListener('pointerdown', onPointerDown);
                track.addEventListener('pointermove', onPointerMove, { passive: false });
                track.addEventListener('pointerup', onPointerUp);
                track.addEventListener('pointercancel', onPointerUp);
                track.addEventListener('click', function (e) {
                    if (!skipClick) return;
                    skipClick = false;
                    e.preventDefault();
                    e.stopPropagation();
                }, true);

                function syncMode() {
                    if (mq.matches) goTo(index, false);
                    else cards.forEach(function (card) { card.classList.remove('is-slide-active'); });
                }

                if (mq.addEventListener) mq.addEventListener('change', syncMode);
                else if (mq.addListener) mq.addListener(syncMode);

                window.addEventListener('resize', function () {
                    if (mq.matches) goTo(index, false);
                });

                syncMode();
                requestAnimationFrame(function () {
                    if (mq.matches) goTo(index, false);
                });
            })();
        })();
    </script>
</body>
</html>
