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
        'privacy' => __('privacy.meta_title'),
        default => __('marketing.meta_title'),
    };
    $description = match ($page) {
        'home' => __('marketing.meta_description'),
        'privacy' => __('privacy.meta_description'),
        default => __('marketing.meta_' . $page . '_description'),
    };
    $ogLocale = match (app()->getLocale()) {
        'sq' => 'sq_AL',
        'de' => 'de_DE',
        default => 'en_US',
    };
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    {{-- SEO --}}
    <title>{{ $title }}</title>
    <meta name="description" content="{{ $description }}">
    <meta name="robots" content="index, follow">
    <meta name="author" content="WORKOM">
    <link rel="canonical" href="{{ url()->current() }}">

    {{-- Open Graph --}}
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $title }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="WORKOM">
    <meta property="og:image" content="{{ asset('images/marketing/workom_logo.png') }}">
    <meta property="og:image:alt" content="WORKOM Business Management Platform">
    <meta property="og:locale" content="{{ $ogLocale }}">

    {{-- Twitter / X --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $title }}">
    <meta name="twitter:description" content="{{ $description }}">
    <meta name="twitter:image" content="{{ asset('images/marketing/workom_logo.png') }}">

    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="manifest" href="{{ asset('marketing.webmanifest') }}">
    <meta name="theme-color" content="#071838">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="WORKOM">
    <link rel="apple-touch-icon" href="{{ asset('icons/marketing/icon-192.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400..600&family=Sora:wght@500..700&display=swap">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{-- ?v= busts the service-worker cache whenever the file changes --}}
    <link rel="stylesheet" href="{{ asset('assets/marketing.css') }}?v={{ filemtime(public_path('assets/marketing.css')) }}">
    <link rel="stylesheet" href="{{ asset('assets/workom.css') }}?v={{ filemtime(public_path('assets/workom.css')) }}">
    @stack('head')
</head>
{{-- inner pages share one background: white with a soft blue light at the top, behind the header --}}
<body class="wo{{ $page !== 'home' ? ' wo--inner' : '' }}">
    <a class="wo-skip" href="#main">Skip</a>

    @php
        $locale = app()->getLocale();
        $navCurrent = fn (bool $on) => $on ? 'class=is-active aria-current=page' : '';
    @endphp
    {{-- On the home page the header sits over the dark hero photo until the visitor scrolls past it --}}
    <header class="wo-top{{ $page === 'home' ? ' wo-top--over' : '' }}" id="woTop">
        <div class="wo-wrap wo-top-row">
            <a href="{{ route('home') }}" class="wo-logo" aria-label="WORKOM — {{ __('marketing.nav_home') }}">
                <img class="wo-logo__dark" src="{{ asset('images/marketing/workom_logo_dark.png') }}" alt="WORKOM" decoding="async" fetchpriority="high" width="1429" height="275">
                @if ($page === 'home')
                    <img class="wo-logo__light" src="{{ asset('images/marketing/workom_logo.png') }}" alt="" decoding="async" width="1423" height="266">
                @endif
            </a>

            <nav class="wo-nav" aria-label="{{ __('marketing.nav_menu') }}">
                <a href="{{ route('home') }}" {!! $navCurrent($page === 'home') !!}>{{ __('marketing.nav_home') }}</a>
                <a href="{{ route('marketing.product') }}" {!! $navCurrent($page === 'product') !!}>{{ __('marketing.nav_product') }}</a>
                <div class="wo-nav-drop">
                    <button type="button" class="wo-nav-drop-trigger{{ $isModulesNav ? ' is-active' : '' }}" aria-expanded="false" aria-controls="woModulesMenu">
                        {{ __('marketing.nav_modules') }}
                        <svg class="wo-nav-caret" viewBox="0 0 12 12" width="12" height="12" aria-hidden="true" focusable="false">
                            <path d="M2.5 4.5 L6 8 L9.5 4.5" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                    <div class="wo-nav-drop-menu" id="woModulesMenu">
                        @foreach (['erp', 'crm', 'hr'] as $mod)
                            <a href="{{ route('marketing.' . $mod) }}" class="wo-nav-drop-item{{ $page === $mod ? ' is-active' : '' }}" @if($page === $mod) aria-current="page" @endif>
                                <span class="wo-nav-drop-item__title">{{ __('marketing.nav_' . $mod) }}</span>
                                <span class="wo-nav-drop-item__desc">{{ __('marketing.nav_' . $mod . '_d') }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
                <a href="{{ route('marketing.faq') }}" {!! $navCurrent($page === 'faq') !!}>{{ __('marketing.nav_faq') }}</a>
                <a href="{{ route('marketing.contact') }}" {!! $navCurrent($page === 'contact') !!}>{{ __('marketing.nav_contact') }}</a>
            </nav>

            <div class="wo-actions">
                <div class="wo-lang" role="group" aria-label="Language">
                    @foreach (['sq', 'en', 'de'] as $lang)
                        <a href="{{ route('marketing.locale', $lang) }}" hreflang="{{ $lang }}" lang="{{ $lang }}" @if($locale === $lang) class="is-active" aria-current="true" @endif>{{ strtoupper($lang) }}</a>
                    @endforeach
                </div>
                <a class="wo-login" href="{{ route('login') }}">{{ __('marketing.nav_login') }}</a>
                <a class="wo-btn wo-btn-primary wo-top-cta" href="{{ route('marketing.contact') }}">
                    <span class="wo-top-cta__long">{{ __('marketing.nav_demo') }}</span>
                    <span class="wo-top-cta__short">{{ __('marketing.nav_demo_short') }}</span>
                </a>
                <button type="button" class="wo-menu" id="woMenu" aria-expanded="false" aria-controls="woDrawer" aria-label="{{ __('marketing.nav_menu') }}" data-label-open="{{ __('marketing.nav_menu') }}" data-label-close="{{ __('marketing.nav_close') }}">
                    <span class="wo-menu-bars" aria-hidden="true"><span></span><span></span></span>
                </button>
            </div>
        </div>

        <div class="wo-drawer" id="woDrawer" hidden>
            <div class="wo-wrap wo-drawer-inner">
                <nav class="wo-drawer-nav" aria-label="{{ __('marketing.nav_menu') }}">
                    <a href="{{ route('home') }}" {!! $navCurrent($page === 'home') !!}>{{ __('marketing.nav_home') }}</a>
                    <a href="{{ route('marketing.product') }}" {!! $navCurrent($page === 'product') !!}>{{ __('marketing.nav_product') }}</a>
                    {{-- Modules: a dropdown like the desktop nav; already open on a module page --}}
                    <div class="wo-drawer-drop{{ $isModulesNav ? ' is-open' : '' }}">
                        <button type="button" class="wo-drawer-drop-trigger{{ $isModulesNav ? ' is-active' : '' }}" aria-expanded="{{ $isModulesNav ? 'true' : 'false' }}" aria-controls="woDrawerModules">
                            {{ __('marketing.nav_modules') }}
                            <svg class="wo-nav-caret" viewBox="0 0 12 12" width="12" height="12" aria-hidden="true" focusable="false">
                                <path d="M2.5 4.5 L6 8 L9.5 4.5" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                        <div class="wo-drawer-sub" id="woDrawerModules">
                            <div class="wo-drawer-sub-inner">
                                @foreach (['erp', 'crm', 'hr'] as $mod)
                                    <a href="{{ route('marketing.' . $mod) }}" {!! $navCurrent($page === $mod) !!}>
                                        <span>{{ __('marketing.nav_' . $mod) }}</span>
                                        <small>{{ __('marketing.nav_' . $mod . '_d') }}</small>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('marketing.faq') }}" {!! $navCurrent($page === 'faq') !!}>{{ __('marketing.nav_faq') }}</a>
                    <a href="{{ route('marketing.contact') }}" {!! $navCurrent($page === 'contact') !!}>{{ __('marketing.nav_contact') }}</a>
                </nav>
                <div class="wo-drawer-foot">
                    <a class="wo-btn wo-btn-primary" href="{{ route('marketing.contact') }}">{{ __('marketing.nav_demo') }}</a>
                    <div class="wo-drawer-meta">
                        <a class="wo-login" href="{{ route('login') }}">{{ __('marketing.nav_login') }}</a>
                        <div class="wo-lang" role="group" aria-label="Language">
                            @foreach (['sq', 'en', 'de'] as $lang)
                                <a href="{{ route('marketing.locale', $lang) }}" hreflang="{{ $lang }}" lang="{{ $lang }}" @if($locale === $lang) class="is-active" aria-current="true" @endif>{{ strtoupper($lang) }}</a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main id="main" class="wo-main">
        @yield('content')
    </main>

    {{-- Footer: the same night blue as the closing CTA, so the two read as one dark block --}}
    @php
        $footerCols = [
            'footer_product' => [
                [route('marketing.product'), __('marketing.nav_product')],
                [route('home') . '#si-funksionon', __('marketing.footer_how')],
                [route('home') . '#prova-falas', __('marketing.trial_label')],
                [route('home') . '#per-ke-eshte', __('marketing.footer_for_who')],
                [route('home') . '#klientet', __('marketing.footer_clients')],
            ],
            'footer_modules' => [
                [route('marketing.erp'), __('marketing.nav_erp')],
                [route('marketing.crm'), __('marketing.nav_crm')],
                [route('marketing.hr'), __('marketing.nav_hr')],
            ],
            'footer_help' => [
                [route('marketing.faq'), __('marketing.nav_faq')],
                [route('marketing.contact'), __('marketing.nav_contact')],
                [route('marketing.privacy'), __('marketing.footer_privacy')],
            ],
        ];
    @endphp
    <footer class="wo-foot">
        <div class="wo-wrap">
            <div class="wo-foot__top">
                <div class="wo-foot__brand">
                    <a href="{{ route('home') }}" class="wo-foot__logo" aria-label="WORKOM — {{ __('marketing.nav_home') }}">
                        <img src="{{ asset('images/marketing/workom_logo.png') }}" alt="WORKOM" width="1423" height="266" loading="lazy" decoding="async">
                    </a>
                    <p>{{ __('marketing.footer_copy') }}</p>
                    <div class="wo-foot__contact">
                        <a href="mailto:{{ __('marketing.contact_email_address') }}">{{ __('marketing.contact_email_address') }}</a>
                        <a href="tel:+{{ __('marketing.phone_wa') }}">{{ __('marketing.phone_display') }}</a>
                    </div>
                </div>

                <div class="wo-foot__cols">
                    @foreach ($footerCols as $title => $links)
                        <nav aria-labelledby="wo-foot-{{ $loop->index }}">
                            <h2 id="wo-foot-{{ $loop->index }}">{{ __('marketing.' . $title) }}</h2>
                            <ul>
                                @foreach ($links as [$href, $label])
                                    <li><a href="{{ $href }}">{{ $label }}</a></li>
                                @endforeach
                            </ul>
                        </nav>
                    @endforeach
                </div>
            </div>

            <div class="wo-foot__bottom">
                <p>&copy; {{ date('Y') }} WORKOM. {{ __('marketing.footer_rights') }}</p>
                @include('marketing.partials.social', ['class' => 'wo-social--dark'])
                <div class="wo-foot__lang" role="group" aria-label="Language">
                    @foreach (['sq', 'en', 'de'] as $lang)
                        <a href="{{ route('marketing.locale', $lang) }}" hreflang="{{ $lang }}" lang="{{ $lang }}" @if ($locale === $lang) class="is-active" aria-current="true" @endif>{{ strtoupper($lang) }}</a>
                    @endforeach
                </div>
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
            var top = document.getElementById('woTop');
            var btn = document.getElementById('woMenu');
            var drawer = document.getElementById('woDrawer');
            var desktopNav = window.matchMedia('(min-width: 1080px)');
            var drawerTimer = null;

            // Header: hairline + solid background once the page scrolls.
            // Over the home hero it stays dark (is-light off) until the hero has scrolled away
            // or the mobile drawer opens.
            var hero = document.querySelector('.wo-hero');
            function syncTop() {
                if (!top) return;
                var y = window.scrollY;
                top.classList.toggle('is-scrolled', y > 8);
                if (top.classList.contains('wo-top--over')) {
                    var pastHero = !hero || y > hero.offsetHeight - top.offsetHeight - 40;
                    top.classList.toggle('is-light', pastHero || document.body.classList.contains('wo-nav-open'));
                }
            }
            syncTop();
            window.addEventListener('scroll', syncTop, { passive: true });
            window.addEventListener('resize', syncTop);

            // Mobile drawer
            function setMenu(open, restoreFocus) {
                if (!btn || !drawer) return;
                var isOpen = btn.getAttribute('aria-expanded') === 'true';
                if (open === isOpen) return;
                window.clearTimeout(drawerTimer);
                btn.setAttribute('aria-expanded', open ? 'true' : 'false');
                btn.setAttribute('aria-label', btn.dataset[open ? 'labelClose' : 'labelOpen']);
                document.body.classList.toggle('wo-nav-open', open);
                syncTop();
                if (open) {
                    drawer.hidden = false;
                    requestAnimationFrame(function () { drawer.classList.add('is-open'); });
                } else {
                    drawer.classList.remove('is-open');
                    drawerTimer = window.setTimeout(function () { drawer.hidden = true; }, 220);
                    if (restoreFocus) btn.focus();
                }
            }
            if (btn && drawer) {
                btn.addEventListener('click', function () {
                    setMenu(btn.getAttribute('aria-expanded') !== 'true');
                });
                drawer.querySelectorAll('a').forEach(function (link) {
                    link.addEventListener('click', function () { setMenu(false); });
                });
                document.addEventListener('keydown', function (e) {
                    if (e.key === 'Escape') setMenu(false, true);
                });
                var onBreakpoint = function () { if (desktopNav.matches) setMenu(false); };
                if (desktopNav.addEventListener) desktopNav.addEventListener('change', onBreakpoint);

                // Drawer "Modules" dropdown
                drawer.querySelectorAll('.wo-drawer-drop').forEach(function (drop) {
                    var trigger = drop.querySelector('.wo-drawer-drop-trigger');
                    trigger.addEventListener('click', function () {
                        var open = !drop.classList.contains('is-open');
                        drop.classList.toggle('is-open', open);
                        trigger.setAttribute('aria-expanded', open ? 'true' : 'false');
                    });
                });
            }

            // Desktop "Modules" disclosure: hover on fine pointers, click/keyboard everywhere.
            function initNavDrop() {
                document.querySelectorAll('.wo-nav-drop').forEach(function (drop) {
                    var trigger = drop.querySelector('.wo-nav-drop-trigger');
                    if (!trigger) return;
                    var closeTimer = null;

                    function setOpen(open) {
                        window.clearTimeout(closeTimer);
                        drop.classList.toggle('is-open', open);
                        trigger.setAttribute('aria-expanded', open ? 'true' : 'false');
                    }

                    if (window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
                        drop.addEventListener('mouseenter', function () { setOpen(true); });
                        drop.addEventListener('mouseleave', function () {
                            closeTimer = window.setTimeout(function () { setOpen(false); }, 120);
                        });
                    }

                    trigger.addEventListener('click', function () {
                        setOpen(!drop.classList.contains('is-open'));
                    });
                    drop.addEventListener('focusout', function (e) {
                        if (!drop.contains(e.relatedTarget)) setOpen(false);
                    });
                    drop.addEventListener('keydown', function (e) {
                        if (e.key === 'Escape' && drop.classList.contains('is-open')) {
                            setOpen(false);
                            trigger.focus();
                        }
                    });
                    document.addEventListener('click', function (e) {
                        if (!drop.contains(e.target)) setOpen(false);
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

            // Cards: the light inside follows the pointer (CSS reads --mx / --my); on leave it glides back.
            if (!reduceMotion && window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
                document.querySelectorAll('.wo-card, .wo-plan, .wo-channel').forEach(function (card) {
                    card.addEventListener('pointermove', function (e) {
                        var r = card.getBoundingClientRect();
                        card.style.setProperty('--mx', (e.clientX - r.left).toFixed(0) + 'px');
                        card.style.setProperty('--my', (e.clientY - r.top).toFixed(0) + 'px');
                    });
                    card.addEventListener('pointerleave', function () {
                        card.style.removeProperty('--mx');
                        card.style.removeProperty('--my');
                    });
                });
            }
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

            // Every page: the blocks of each section fade in and slide up as they scroll into view,
            // one after another. The home hero stays put; .wo-animate sections have their own motion.
            // Once shown, the classes come off so the blocks keep their own styles.
            if (!reduceMotion && 'IntersectionObserver' in window) {
                var revealIo = new IntersectionObserver(function (entries) {
                    entries.forEach(function (entry) {
                        if (!entry.isIntersecting) return;
                        var el = entry.target;
                        revealIo.unobserve(el);
                        el.classList.add('is-inview');
                        window.setTimeout(function () {
                            el.classList.remove('wo-reveal', 'is-inview');
                            el.style.removeProperty('--reveal-delay');
                        }, 1100);
                    });
                }, { threshold: 0, rootMargin: '0px 0px -10% 0px' });

                document.querySelectorAll('.wo-main section:not(.wo-hero):not(.wo-animate), .wo-main > header, .wo-legal__head, .wo-legal__body').forEach(function (group) {
                    if (group.closest('.wo-reveal')) return;
                    var inner = group.classList.contains('wo-wrap') ? group : (group.querySelector(':scope > .wo-wrap') || group);
                    Array.prototype.forEach.call(inner.children, function (el, i) {
                        if (el.matches('.wo-animate, .wo-sr, script, [hidden]')) return;
                        el.classList.add('wo-reveal');
                        el.style.setProperty('--reveal-delay', Math.min(i, 4) * 90 + 'ms');
                        revealIo.observe(el);
                    });
                });
            }

            // FAQ: one answer open at a time; opening a question closes the one that was open.
            var faqItems = Array.prototype.slice.call(document.querySelectorAll('.wo-faq details'));

            // the wanted state is kept right away (data-open), so quick clicks never leave two answers open
            function setFaq(details, open) {
                var panel = details.querySelector('.wo-faq-panel');
                if (open === (details.dataset.open === '1')) return;
                details.dataset.open = open ? '1' : '0';

                if (reduceMotion) {
                    details.open = open;
                    details.classList.toggle('is-open', open);
                    return;
                }

                if (open) {
                    details.open = true;
                    requestAnimationFrame(function () {
                        if (details.dataset.open === '1') details.classList.add('is-open');
                    });
                    return;
                }

                details.classList.remove('is-open');
                var done = false;
                var finish = function () {
                    // reopened before the panel finished closing: leave it open
                    if (done || details.dataset.open === '1') return;
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

            faqItems.forEach(function (details) {
                var summary = details.querySelector('summary');
                if (!summary || !details.querySelector('.wo-faq-panel')) return;

                details.classList.add('wo-faq-ready');
                details.dataset.open = details.open ? '1' : '0';
                if (details.open) details.classList.add('is-open');

                summary.addEventListener('click', function (e) {
                    e.preventDefault();
                    var opening = details.dataset.open !== '1';
                    if (opening) {
                        faqItems.forEach(function (other) {
                            if (other !== details) setFaq(other, false);
                        });
                    }
                    setFaq(details, opening);
                });
            });
        })();
    </script>
    @stack('scripts')
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function () {
                navigator.serviceWorker.register(@json(asset('marketing-sw.js')), { scope: '/' }).catch(function () {});
            });
        }
    </script>
</body>
</html>
