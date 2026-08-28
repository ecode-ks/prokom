@php
    $page = $page ?? 'home';
    $modulePages = ['erp', 'crm', 'hr'];
    $isModulesNav = in_array($page, $modulePages, true);
    $title = match ($page) {
        'product' => __('marketing.meta_product'),
        'erp' => __('marketing.meta_erp'),
        'crm' => __('marketing.meta_crm'),
        'hr' => __('marketing.meta_hr'),
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
    @include('partials.favicon')
    @include('marketing.partials.pwa')
    <meta name="description" content="{{ __('marketing.meta_description') }}">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:title" content="{{ $title }}">
    <meta property="og:description" content="{{ __('marketing.meta_description') }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Source+Sans+3:wght@400;500;600;700&family=Sora:wght@500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('assets/marketing.css') }}">
    @stack('head')
</head>
<body class="wo">
    <a class="wo-skip" href="#main">Skip</a>

    <header class="wo-top">
        <div class="wo-wrap wo-top-row">
            <a href="{{ route('home') }}" class="wo-logo">
                <img src="{{ asset('images/marketing/workom_logo_dark.png') }}" alt="WORKOM" decoding="async" fetchpriority="high" width="170" height="48">
            </a>

            <nav class="wo-tabs" aria-label="Primary">
                <a href="{{ route('home') }}" class="{{ $page === 'home' ? 'is-active' : '' }}">{{ __('marketing.nav_home') }}</a>
                <a href="{{ route('marketing.product') }}" class="{{ $page === 'product' ? 'is-active' : '' }}">{{ __('marketing.nav_product') }}</a>
                <div class="wo-nav-drop {{ $isModulesNav ? 'is-active' : '' }}">
                    <button type="button" class="wo-nav-drop-trigger {{ $isModulesNav ? 'is-active' : '' }}" aria-haspopup="true" aria-expanded="false">
                        <span class="wo-nav-drop-trigger__label">{{ __('marketing.nav_modules') }}</span>
                        <svg class="wo-nav-caret" viewBox="0 0 12 12" width="12" height="12" aria-hidden="true" focusable="false">
                            <path d="M2.5 4.5 L6 8 L9.5 4.5" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                    <div class="wo-nav-drop-menu" role="menu">
                        <a href="{{ route('marketing.erp') }}" class="wo-nav-drop-item {{ $page === 'erp' ? 'is-active' : '' }}" role="menuitem">
                            <span class="wo-nav-drop-item__title">{{ __('marketing.nav_erp') }}</span>
                            <span class="wo-nav-drop-item__desc">{{ __('marketing.nav_erp_d') }}</span>
                        </a>
                        <a href="{{ route('marketing.crm') }}" class="wo-nav-drop-item {{ $page === 'crm' ? 'is-active' : '' }}" role="menuitem">
                            <span class="wo-nav-drop-item__title">{{ __('marketing.nav_crm') }}</span>
                            <span class="wo-nav-drop-item__desc">{{ __('marketing.nav_crm_d') }}</span>
                        </a>
                        <a href="{{ route('marketing.hr') }}" class="wo-nav-drop-item {{ $page === 'hr' ? 'is-active' : '' }}" role="menuitem">
                            <span class="wo-nav-drop-item__title">{{ __('marketing.nav_hr') }}</span>
                            <span class="wo-nav-drop-item__desc">{{ __('marketing.nav_hr_d') }}</span>
                        </a>
                    </div>
                </div>
                <a href="{{ route('marketing.faq') }}" class="{{ $page === 'faq' ? 'is-active' : '' }}">{{ __('marketing.nav_faq') }}</a>
                <a href="{{ route('marketing.contact') }}" class="{{ $page === 'contact' ? 'is-active' : '' }}">{{ __('marketing.nav_contact') }}</a>
            </nav>

            <div class="wo-actions">
                <div class="wo-lang" role="group" aria-label="Language">
                    <a href="{{ route('marketing.locale', 'sq') }}" class="{{ app()->getLocale() === 'sq' ? 'is-active' : '' }}">SQ</a>
                    <a href="{{ route('marketing.locale', 'en') }}" class="{{ app()->getLocale() === 'en' ? 'is-active' : '' }}">EN</a>
                </div>
                <a class="wo-btn wo-btn-ghost wo-hide-sm wo-btn-login" href="{{ route('login') }}" aria-label="{{ __('marketing.nav_login') }}" title="{{ __('marketing.nav_login') }}">
                    <svg class="wo-btn-icon text-primary" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                        <path fill="currentColor" d="M12 12a4.5 4.5 0 1 0-4.5-4.5A4.5 4.5 0 0 0 12 12zm0 2.25c-3.6 0-6.75 1.8-6.75 4.05V20h13.5v-1.7c0-2.25-3.15-4.05-6.75-4.05z"/>
                    </svg>
                </a>
                <button type="button" class="wo-menu" id="woMenu" aria-expanded="false" aria-controls="woDrawer" aria-label="{{ __('marketing.nav_menu') }}">
                    <span class="wo-menu-bars" aria-hidden="true">
                        <span></span><span></span><span></span>
                    </span>
                </button>
            </div>
        </div>
        <div class="wo-nav-backdrop" id="woNavBackdrop" hidden aria-hidden="true"></div>
        <nav class="wo-drawer" id="woDrawer" aria-label="Mobile">
            <div class="wo-wrap wo-drawer-inner">
                <div class="wo-drawer-nav">
                    <a href="{{ route('home') }}" class="{{ $page === 'home' ? 'is-active' : '' }}">{{ __('marketing.nav_home') }}</a>
                    <a href="{{ route('marketing.product') }}" class="{{ $page === 'product' ? 'is-active' : '' }}">{{ __('marketing.nav_product') }}</a>
                    <details class="wo-drawer-modules{{ $isModulesNav ? ' is-open' : '' }}" @if($isModulesNav) open @endif>
                        <summary class="wo-drawer-modules__trigger">
                            <span class="wo-drawer-modules__label">{{ __('marketing.nav_modules') }}</span>
                            <svg class="wo-drawer-modules__caret" viewBox="0 0 12 12" width="12" height="12" aria-hidden="true" focusable="false">
                                <path d="M2.5 4.5 L6 8 L9.5 4.5" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </summary>
                        <div class="wo-drawer-sub">
                            <a href="{{ route('marketing.erp') }}" class="{{ $page === 'erp' ? 'is-active' : '' }}">{{ __('marketing.nav_erp') }}</a>
                            <a href="{{ route('marketing.crm') }}" class="{{ $page === 'crm' ? 'is-active' : '' }}">{{ __('marketing.nav_crm') }}</a>
                            <a href="{{ route('marketing.hr') }}" class="{{ $page === 'hr' ? 'is-active' : '' }}">{{ __('marketing.nav_hr') }}</a>
                        </div>
                    </details>
                    <a href="{{ route('marketing.faq') }}" class="{{ $page === 'faq' ? 'is-active' : '' }}">{{ __('marketing.nav_faq') }}</a>
                    <a href="{{ route('marketing.contact') }}" class="{{ $page === 'contact' ? 'is-active' : '' }}">{{ __('marketing.nav_contact') }}</a>
                </div>
                <div class="wo-drawer-cta">
                    <a class="wo-btn wo-btn-ghost" href="{{ route('login') }}">{{ __('marketing.nav_login') }}</a>
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
                        <img
                            class="wo-lazy"
                            src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8'%3E%3Crect width='8' height='8' fill='%23071838'/%3E%3C/svg%3E"
                            data-wo-src="{{ asset('images/marketing/workom_logo.png') }}"
                            alt="WORKOM"
                            width="220"
                            height="56"
                            loading="lazy"
                            decoding="async"
                        >
                    </a>
                    <p class="wo-footer-copy">{{ __('marketing.footer_copy') }}</p>
                    @include('marketing.partials.social-links', ['ariaLabel' => __('marketing.footer_social')])
                </div>

                <div class="wo-footer-col">
                    <h4>{{ __('marketing.footer_modules') }}</h4>
                    <nav aria-label="{{ __('marketing.footer_modules') }}">
                        <a href="{{ route('marketing.erp') }}">{{ __('marketing.nav_erp') }}</a>
                        <a href="{{ route('marketing.crm') }}">{{ __('marketing.nav_crm') }}</a>
                        <a href="{{ route('marketing.hr') }}">{{ __('marketing.nav_hr') }}</a>
                        <a href="{{ route('home') }}#si-funksionon">{{ __('marketing.footer_how') }}</a>
                    </nav>
                </div>

                <div class="wo-footer-col wo-footer-col--end">
                    <h4>{{ __('marketing.footer_product') }}</h4>
                    <nav aria-label="{{ __('marketing.footer_product') }}">
                        <a href="{{ route('home') }}#cmimet">{{ __('marketing.footer_pricing') }}</a>
                        <a href="{{ route('home') }}#per-ke-eshte">{{ __('marketing.footer_for_who') }}</a>
                        <a href="{{ route('marketing.contact') }}">{{ __('marketing.nav_contact') }}</a>
                        <a href="{{ route('home') }}#klientet">{{ __('marketing.footer_clients') }}</a>
                    </nav>
                </div>
            </div>

            <div class="wo-footer-bottom">
                <p class="wo-copy">&copy; {{ date('Y') }} WORKOM. {{ __('marketing.footer_rights') }}</p>
                <p class="wo-footer-bottom-tag">{{ __('marketing.footer_bottom_tag') }}</p>
            </div>
        </div>
    </footer>

    <div class="wo-lightbox" id="woLightbox" hidden aria-hidden="true" role="dialog" aria-modal="true" aria-label="{{ __('marketing.shot_placeholder') }}">
        <div class="wo-lightbox-inner">
            <button type="button" class="wo-lightbox-close" id="woLightboxClose" aria-label="{{ __('marketing.lightbox_close') }}">&times;</button>
            <img src="" alt="" id="woLightboxImg">
            <p class="wo-lightbox-label" id="woLightboxLabel"></p>
        </div>
    </div>

    <script>
        (function () {
            var btn = document.getElementById('woMenu');
            var drawer = document.getElementById('woDrawer');
            var backdrop = document.getElementById('woNavBackdrop');
            var drawerCloseTimer = null;
            function setMenu(open) {
                if (!btn || !drawer) return;
                window.clearTimeout(drawerCloseTimer);
                if (open) {
                    drawer.classList.remove('is-closing');
                    drawer.classList.add('is-open');
                    btn.classList.add('is-open');
                    btn.setAttribute('aria-expanded', 'true');
                    document.body.classList.add('wo-nav-open');
                    if (backdrop) {
                        backdrop.hidden = false;
                        backdrop.setAttribute('aria-hidden', 'false');
                    }
                } else {
                    if (!drawer.classList.contains('is-open') && !drawer.classList.contains('is-closing')) return;
                    drawer.classList.add('is-closing');
                    drawer.classList.remove('is-open');
                    btn.classList.remove('is-open');
                    btn.setAttribute('aria-expanded', 'false');
                    document.body.classList.remove('wo-nav-open');
                    if (backdrop) backdrop.setAttribute('aria-hidden', 'true');
                    drawerCloseTimer = window.setTimeout(function () {
                        drawer.classList.remove('is-closing');
                        if (backdrop) backdrop.hidden = true;
                    }, 380);
                }
            }
            if (btn && drawer) {
                btn.addEventListener('click', function () {
                    setMenu(!drawer.classList.contains('is-open'));
                });
                drawer.querySelectorAll('a').forEach(function (link) {
                    link.addEventListener('click', function () { setMenu(false); });
                });
                if (backdrop) {
                    backdrop.addEventListener('click', function () { setMenu(false); });
                }
                document.addEventListener('keydown', function (e) {
                    if (e.key === 'Escape') setMenu(false);
                });
                window.addEventListener('resize', function () {
                    if (window.matchMedia('(min-width: 920px)').matches) setMenu(false);
                });
            }

            function initNavDrop() {
                if (document.documentElement.dataset.woNavDropReady === '1') return;
                document.documentElement.dataset.woNavDropReady = '1';

                document.querySelectorAll('.wo-nav-drop').forEach(function (drop) {
                    var trigger = drop.querySelector('.wo-nav-drop-trigger');
                    if (!trigger) return;

                    function setOpen(open) {
                        drop.classList.toggle('is-open', open);
                        trigger.setAttribute('aria-expanded', open ? 'true' : 'false');
                    }

                    var hoverCapable = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
                    if (hoverCapable) {
                        drop.addEventListener('mouseenter', function () { setOpen(true); });
                        drop.addEventListener('mouseleave', function () { setOpen(false); });
                    }

                    trigger.addEventListener('click', function (e) {
                        e.preventDefault();
                        e.stopPropagation();
                        var willOpen = !drop.classList.contains('is-open');
                        document.querySelectorAll('.wo-nav-drop.is-open').forEach(function (other) {
                            if (other === drop) return;
                            other.classList.remove('is-open');
                            var otherTrigger = other.querySelector('.wo-nav-drop-trigger');
                            if (otherTrigger) otherTrigger.setAttribute('aria-expanded', 'false');
                        });
                        setOpen(willOpen);
                    });

                    drop.addEventListener('focusin', function () { setOpen(true); });
                    drop.addEventListener('focusout', function (e) {
                        if (!drop.contains(e.relatedTarget)) setOpen(false);
                    });
                });

                document.addEventListener('click', function (e) {
                    if (e.target.closest('.wo-nav-drop')) return;
                    document.querySelectorAll('.wo-nav-drop.is-open').forEach(function (drop) {
                        drop.classList.remove('is-open');
                        var trigger = drop.querySelector('.wo-nav-drop-trigger');
                        if (trigger) trigger.setAttribute('aria-expanded', 'false');
                    });
                });
            }

            function initDrawerModules() {
                document.querySelectorAll('.wo-drawer-modules').forEach(function (details) {
                    var summary = details.querySelector('.wo-drawer-modules__trigger');
                    var panel = details.querySelector('.wo-drawer-sub');
                    if (!summary || !panel || details.dataset.woModulesReady === '1') return;
                    details.dataset.woModulesReady = '1';
                    details.classList.add('wo-drawer-modules-ready');
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
                            details.classList.remove('is-closing');
                            requestAnimationFrame(function () {
                                details.classList.add('is-open');
                            });
                            return;
                        }

                        details.classList.add('is-closing');
                        details.classList.remove('is-open');
                        var done = false;
                        var finish = function () {
                            if (done) return;
                            done = true;
                            details.open = false;
                            details.classList.remove('is-closing');
                        };
                        panel.addEventListener('transitionend', function onEnd(ev) {
                            if (ev.target !== panel) return;
                            panel.removeEventListener('transitionend', onEnd);
                            finish();
                        });
                        window.setTimeout(finish, 420);
                    });
                });
            }

            var lightbox = document.getElementById('woLightbox');
            var lightboxImg = document.getElementById('woLightboxImg');
            var lightboxLabel = document.getElementById('woLightboxLabel');
            var lightboxClose = document.getElementById('woLightboxClose');

            function hydrateLazyImg(img) {
                var src = img.getAttribute('data-wo-src');
                if (!src) return;
                img.onload = function () {
                    img.classList.add('is-loaded');
                    img.onload = null;
                };
                img.src = src;
                img.removeAttribute('data-wo-src');
                if (img.complete) img.classList.add('is-loaded');
            }

            var lazyImgs = document.querySelectorAll('img[data-wo-src]');
            if ('IntersectionObserver' in window && lazyImgs.length) {
                var lazyIo = new IntersectionObserver(function (entries) {
                    entries.forEach(function (entry) {
                        if (!entry.isIntersecting) return;
                        hydrateLazyImg(entry.target);
                        lazyIo.unobserve(entry.target);
                    });
                }, { rootMargin: '180px 0px', threshold: 0.01 });
                lazyImgs.forEach(function (img) { lazyIo.observe(img); });
            } else {
                lazyImgs.forEach(hydrateLazyImg);
            }

            function openLightbox(src, label) {
                if (!lightbox || !lightboxImg) return;
                lightboxImg.src = src;
                lightboxImg.alt = label || '';
                lightboxImg.classList.remove('is-zoomed');
                if (lightboxLabel) lightboxLabel.textContent = label || '';
                lightbox.hidden = false;
                lightbox.classList.add('is-open');
                lightbox.setAttribute('aria-hidden', 'false');
                document.body.classList.add('wo-lightbox-open');
            }

            function closeLightbox() {
                if (!lightbox || !lightboxImg) return;
                lightbox.classList.remove('is-open');
                lightbox.hidden = true;
                lightbox.setAttribute('aria-hidden', 'true');
                lightboxImg.src = '';
                lightboxImg.classList.remove('is-zoomed');
                document.body.classList.remove('wo-lightbox-open');
            }

            document.querySelectorAll('[data-wo-lightbox]').forEach(function (el) {
                el.addEventListener('click', function () {
                    openLightbox(el.getAttribute('data-wo-lightbox'), el.getAttribute('data-wo-lightbox-label') || '');
                });
            });

            if (lightboxClose) lightboxClose.addEventListener('click', closeLightbox);
            if (lightbox) {
                lightbox.addEventListener('click', function (e) {
                    if (e.target === lightbox) closeLightbox();
                });
            }
            if (lightboxImg) {
                lightboxImg.addEventListener('click', function (e) {
                    e.stopPropagation();
                    lightboxImg.classList.toggle('is-zoomed');
                });
            }
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && lightbox && lightbox.classList.contains('is-open')) {
                    closeLightbox();
                }
            });

            var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            initNavDrop();
            initDrawerModules();
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
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function () {
                navigator.serviceWorker.register(@json(asset('marketing-sw.js')), { scope: '/' }).catch(function () {});
            });
        }
    </script>
</body>
</html>
