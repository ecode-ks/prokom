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
            <div class="wo-footer-main">
                <div class="wo-footer-brand">
                    <a href="{{ route('home') }}" class="wo-logo wo-logo-footer">
                        <img src="{{ asset('images/marketing/workom_logo.png') }}" alt="WorkOM" width="160" height="160">
                    </a>
                    <p class="wo-footer-tagline">{{ __('marketing.tagline') }}</p>
                    <p class="wo-footer-copy">{{ __('marketing.footer_copy') }}</p>
                    @php
                        $socialLinks = array_filter([
                            'instagram' => __('marketing.social_instagram'),
                            'linkedin' => __('marketing.social_linkedin'),
                            'tiktok' => __('marketing.social_tiktok'),
                        ]);
                    @endphp
                    @if ($socialLinks)
                        <div class="wo-footer-social">
                            <span class="wo-footer-social-label">{{ __('marketing.footer_social') }}</span>
                            <div class="wo-footer-social-links">
                                @if (!empty(__('marketing.social_instagram')))
                                    <a href="{{ __('marketing.social_instagram') }}" target="_blank" rel="noopener noreferrer" aria-label="Instagram">
                                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 2h10a5 5 0 0 1 5 5v10a5 5 0 0 1-5 5H7a5 5 0 0 1-5-5V7a5 5 0 0 1 5-5zm0 2a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3V7a3 3 0 0 0-3-3H7zm5 3.5A5.5 5.5 0 1 1 6.5 13 5.5 5.5 0 0 1 12 7.5zm0 2A3.5 3.5 0 1 0 15.5 13 3.5 3.5 0 0 0 12 9.5zM17.75 6.25a1.25 1.25 0 1 1-1.25 1.25 1.25 1.25 0 0 1 1.25-1.25z"/></svg>
                                    </a>
                                @endif
                                @if (!empty(__('marketing.social_linkedin')))
                                    <a href="{{ __('marketing.social_linkedin') }}" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn">
                                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4.98 3.5a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0zM.5 8.25h4.5V23.5H.5V8.25zM8.25 8.25h4.31v2.09h.06c.6-1.16 2.07-2.38 4.26-2.38 4.62 0 5.47 3.04 5.47 7v8.54h-4.56v-7.57c0-1.8-.03-4.12-2.51-4.12-2.51 0-2.89 1.96-2.89 3.99v7.7H8.25V8.25z"/></svg>
                                    </a>
                                @endif
                                @if (!empty(__('marketing.social_tiktok')))
                                    <a href="{{ __('marketing.social_tiktok') }}" target="_blank" rel="noopener noreferrer" aria-label="TikTok">
                                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M16 3c.6 3.1 2.6 5.2 5 5.5v4.1a8.3 8.3 0 0 1-5-.9v7.4a6.8 6.8 0 1 1-6.8-6.8c.4 0 .7 0 1.1.1v4.3c-.3-.1-.6-.2-1-.2a2.5 2.5 0 1 0 2.5 2.5V3h3.2z"/></svg>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                <div class="wo-footer-col">
                    <h4>{{ __('marketing.footer_links') }}</h4>
                    <nav aria-label="Footer">
                        <a href="{{ route('home') }}">{{ __('marketing.nav_home') }}</a>
                        <a href="{{ route('marketing.product') }}">{{ __('marketing.nav_product') }}</a>
                        <a href="{{ route('marketing.faq') }}">{{ __('marketing.nav_faq') }}</a>
                        <a href="{{ route('marketing.contact') }}">{{ __('marketing.nav_contact') }}</a>
                    </nav>
                </div>

                <div class="wo-footer-col">
                    <h4>{{ __('marketing.footer_contact') }}</h4>
                    <a href="https://wa.me/{{ __('marketing.phone_wa') }}" target="_blank" rel="noopener noreferrer">{{ __('marketing.whatsapp') }} · {{ __('marketing.phone_display') }}</a>
                    <a href="{{ route('marketing.contact') }}">{{ __('marketing.nav_demo') }}</a>
                    <a href="{{ route('login') }}">{{ __('marketing.footer_login') }}</a>
                    <a href="{{ route('register') }}">{{ __('marketing.footer_register') }}</a>
                </div>
            </div>

            <div class="wo-footer-bottom">
                <p class="wo-copy">&copy; {{ date('Y') }} WorkOM. {{ __('marketing.footer_rights') }}</p>
            </div>
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

                var mq = window.matchMedia('(max-width: 639px)');
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
