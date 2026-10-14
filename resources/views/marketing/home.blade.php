@extends('marketing.layout')

@push('head')
    <link rel="preload" as="image" href="{{ asset('images/marketing/hero_site.jpg') }}" fetchpriority="high">
@endpush

@section('content')
@php
    $clients = [
        ['file' => 'clients/alphatech.webp', 'alt' => 'AlphaTech'],
        ['file' => 'clients/bibaj.webp', 'alt' => 'Bibaj'],
        ['file' => 'clients/gashiglasstech.webp', 'alt' => 'Gashi Glass Tech'],
        ['file' => 'clients/laconcept.webp', 'alt' => 'LA Concept'],
        ['file' => 'clients/mobelpro.webp', 'alt' => 'Mobel Pro'],
        ['file' => 'clients/mstudio.webp', 'alt' => 'M Studio'],
        ['file' => 'clients/veluna.webp', 'alt' => 'Veluna'],
    ];
@endphp

{{-- The pin holds the hero in place for a stretch of scroll (desktop) while the parallax plays --}}
<div class="wo-hero-pin">
<section class="wo-hero" aria-labelledby="wo-hero-title">
    <div class="wo-hero__bg" aria-hidden="true">
        <div class="wo-hero__photo" style="background-image: url('{{ asset('images/marketing/hero_site.jpg') }}')"></div>
        <div class="wo-hero__tint"></div>
        <div class="wo-hero__shade"></div>
        <div class="wo-hero__grain"></div>
    </div>

    <div class="wo-wrap wo-hero__copy">
        <h1 id="wo-hero-title">{{ __('marketing.hero_title_main') }} <em>{{ __('marketing.hero_title_accent') }}</em></h1>
        <p class="wo-hero__lead">{{ __('marketing.hero_lead') }}</p>
        <div class="wo-hero__actions">
            <a class="wo-btn wo-btn-primary" href="{{ route('marketing.contact') }}">{{ __('marketing.hero_cta_demo') }}</a>
            <a class="wo-btn wo-btn-glass" href="{{ route('marketing.product') }}">{{ __('marketing.hero_cta_product') }}</a>
        </div>
    </div>

    <div class="wo-hero__horizon" aria-hidden="true"></div>
</section>
<div class="wo-hero-pin__space" aria-hidden="true"></div>
</div>

<section class="wo-wrap wo-trust" id="klientet" aria-labelledby="wo-trust-label">
    <p class="wo-trust__label" id="wo-trust-label">{{ __('marketing.clients_label') }}</p>
    <ul class="wo-trust__logos">
        @foreach ($clients as $client)
            @if (is_file(public_path('images/marketing/' . $client['file'])))
                <li><img src="{{ asset('images/marketing/' . $client['file']) }}" alt="{{ $client['alt'] }}" loading="lazy" decoding="async"></li>
            @endif
        @endforeach
    </ul>
</section>

<div class="wo-wrap">
    <section class="wo-section wo-animate" id="per-ke-eshte">
        <span class="wo-label wo-animate-item">{{ __('marketing.who_label') }}</span>
        <h2 class="wo-animate-item">{{ __('marketing.who_title') }}</h2>
        <p class="wo-lead wo-animate-item">{{ __('marketing.who_text') }}</p>

        <div class="wo-module-cards wo-animate-item" style="margin-top:2rem">
            <a href="{{ route('marketing.erp') }}" class="wo-module-card">
                <h3>{{ __('marketing.nav_erp') }}</h3>
                <p>{{ __('marketing.erp_text') }}</p>
                <span class="wo-module-card-cta">{{ __('marketing.module_open') }} →</span>
            </a>
            <a href="{{ route('marketing.crm') }}" class="wo-module-card">
                <h3>{{ __('marketing.nav_crm') }}</h3>
                <p>{{ __('marketing.crm_text') }}</p>
                <span class="wo-module-card-cta">{{ __('marketing.module_open') }} →</span>
            </a>
            <a href="{{ route('marketing.hr') }}" class="wo-module-card">
                <h3>{{ __('marketing.nav_hr') }}</h3>
                <p>{{ __('marketing.hr_text') }}</p>
                <span class="wo-module-card-cta">{{ __('marketing.module_open') }} →</span>
            </a>
        </div>
    </section>

</div>

{{-- How it works: scrollytelling. A line fills as the visitor scrolls; each step plays its own illustrated scene. --}}
<section class="wo-how" id="si-funksionon" aria-labelledby="wo-how-title">
    <div class="wo-wrap">
        <div class="wo-how__intro">
            <span class="wo-label">{{ __('marketing.how_label') }}</span>
            <h2 id="wo-how-title">{{ __('marketing.how_title') }}</h2>
            <p class="wo-lead">{{ __('marketing.how_lead') }}</p>
        </div>

        <div class="wo-how__grid">
        <div class="wo-how__stage" aria-hidden="true">
            @foreach (range(1, 5) as $n)
                @include('marketing.partials.how-scene', ['n' => $n, 'shot' => $n - 1])
            @endforeach
        </div>

        <div class="wo-how__rail" data-how>
            <span class="wo-how__track" aria-hidden="true"><span class="wo-how__fill"></span></span>
            <ol class="wo-how__steps">
            @foreach (range(1, 5) as $n)
                <li class="wo-how__step" data-how-step="{{ $n - 1 }}">
                    <span class="wo-how__dot" aria-hidden="true">{{ str_pad($n, 2, '0', STR_PAD_LEFT) }}</span>
                    <div class="wo-how__body">
                        <h3>{{ __('marketing.how_' . $n . '_t') }}</h3>
                        <p>{{ __('marketing.how_' . $n) }}</p>
                        <div class="wo-how__inline">
                            @include('marketing.partials.how-scene', ['n' => $n])
                        </div>
                    </div>
                </li>
            @endforeach
            </ol>
        </div>
        </div>
    </div>
</section>

{{-- Pricing: packages come from the admin panel (packages table), never hard-coded here --}}
@php
    $locale = app()->getLocale();
    $money = function ($amount) use ($locale) {
        $amount = (float) $amount;
        $decimals = fmod($amount, 1.0) == 0.0 ? 0 : 2;
        return $locale === 'en'
            ? '€' . number_format($amount, $decimals, '.', ',')
            : number_format($amount, $decimals, ',', '.') . ' €';
    };
    $maxStaff = (int) $packages->max('staff_limit');
    $startStaff = (int) ($packages->first()->staff_limit ?? 1);
@endphp
<section class="wo-pricing" id="cmimet" aria-labelledby="wo-pricing-title">
    <div class="wo-wrap">
        <div class="wo-pricing__head">
            <span class="wo-label">{{ __('marketing.pricing_label') }}</span>
            <h2 id="wo-pricing-title">{{ __('marketing.pricing_title') }}</h2>
            <p class="wo-lead">{{ __('marketing.pricing_lead') }}</p>
        </div>

        @if ($packages->isNotEmpty())
            <div class="wo-pricing__team" data-pricing
                 data-one="{{ __('marketing.pricing_team_one') }}"
                 data-many="{{ __('marketing.pricing_team_many') }}"
                 data-over="{{ __('marketing.pricing_team_over', ['count' => $maxStaff]) }}">
                <label for="woTeamSize">{{ __('marketing.pricing_team_label') }}</label>
                <output for="woTeamSize" id="woTeamOut" aria-live="polite">{{ __('marketing.pricing_team_many', ['count' => $startStaff]) }}</output>
                <input type="range" id="woTeamSize" min="1" max="{{ $maxStaff + 1 }}" step="1" value="{{ $startStaff }}">
            </div>

            <ul class="wo-pricing__cards" role="list">
                @foreach ($packages as $package)
                    @php
                        $months = max(1, (int) $package->months);
                        $features = collect(preg_split('/\r\n|\r|\n/', (string) $package->description))
                            ->map(fn ($line) => trim($line, " \t-•*"))
                            ->filter()
                            ->values();
                    @endphp
                    <li class="wo-plan" data-limit="{{ (int) $package->staff_limit }}">
                        <span class="wo-plan__fit">{{ __('marketing.pricing_fit') }}</span>
                        <h3 class="wo-plan__title">{{ $package->title }}</h3>
                        <p class="wo-plan__staff">{{ __('marketing.pricing_staff', ['count' => (int) $package->staff_limit]) }}</p>
                        <p class="wo-plan__price">
                            <strong>{{ $money($package->price) }}</strong>
                            <span>{{ $months === 1 ? __('marketing.pricing_period_one') : __('marketing.pricing_period', ['count' => $months]) }}</span>
                        </p>
                        @if ($months > 1)
                            <p class="wo-plan__monthly">{{ __('marketing.pricing_per_month', ['price' => $money(round($package->price / $months, 2))]) }}</p>
                        @endif
                        @if ($features->isNotEmpty())
                            <ul class="wo-plan__features">
                                @foreach ($features as $feature)
                                    <li>{{ $feature }}</li>
                                @endforeach
                            </ul>
                        @endif
                        <a class="wo-btn wo-btn-ghost wo-plan__cta" href="{{ route('register') }}">{{ __('marketing.pricing_cta') }}</a>
                    </li>
                @endforeach
            </ul>

            <p class="wo-pricing__more" data-pricing-more>
                <span>{{ __('marketing.pricing_more', ['count' => $maxStaff]) }}</span>
                <a href="{{ route('marketing.contact') }}">{{ __('marketing.pricing_more_cta') }} →</a>
            </p>
        @else
            <div class="wo-pricing__empty">
                <p>{{ __('marketing.pricing_empty') }}</p>
                <a class="wo-btn wo-btn-primary" href="{{ route('marketing.contact') }}">{{ __('marketing.pricing_more_cta') }}</a>
            </div>
        @endif
    </div>
</section>

{{-- Closing CTA, echoing the hero --}}
<section class="wo-close" id="demo" aria-labelledby="wo-close-title">
    <div class="wo-wrap wo-close__inner">
        <span class="wo-label">{{ __('marketing.cta_label') }}</span>
        <h2 id="wo-close-title">{{ __('marketing.cta_title') }}</h2>
        <p class="wo-close__lead">{{ __('marketing.cta_lead') }}</p>
        <div class="wo-hero__actions">
            <a class="wo-btn wo-btn-primary" href="{{ route('marketing.contact') }}">{{ __('marketing.nav_demo') }}</a>
            <a class="wo-btn wo-btn-glass" href="https://wa.me/{{ __('marketing.phone_wa') }}" target="_blank" rel="noopener noreferrer">{{ __('marketing.whatsapp_cta') }}</a>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    // Hero parallax.
    // Desktop: the hero is pinned while the pin's spacer scrolls past (D px). During that stretch the photo
    // slowly zooms and the copy rises and fades; afterwards the hero leaves with the photo still drifting.
    // Phone (no pin, D = 0): the photo drifts slower than the page and the copy lags and fades.
    (function () {
        var hero = document.querySelector('.wo-hero');
        var space = document.querySelector('.wo-hero-pin__space');
        if (!hero || window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
        var ticking = false;
        var clamp = function (v) { return Math.min(1, Math.max(0, v)); };

        function update() {
            ticking = false;
            var y = window.scrollY;
            var h = hero.offsetHeight;
            var D = space ? space.offsetHeight : 0;
            if (y > D + h) return; // hero is off screen, nothing to move

            var py, ps, cy, co;
            if (D > 0) {
                var t = clamp(y / D);          // progress through the pinned stretch
                var after = Math.max(0, y - D); // scroll after the pin releases
                ps = 1.04 + 0.12 * t;
                py = -24 * t + after * 0.35;
                cy = -70 * t + after * 0.18;
                co = 1 - clamp((t - 0.3) / 0.7);
            } else {
                ps = 1.04;
                py = y * 0.3;
                cy = y * 0.12;
                co = 1 - clamp(y / (h * 1.1));
            }
            hero.style.setProperty('--py', py.toFixed(1) + 'px');
            hero.style.setProperty('--ps', ps.toFixed(4));
            hero.style.setProperty('--cy', cy.toFixed(1) + 'px');
            hero.style.setProperty('--co', co.toFixed(3));
        }
        window.addEventListener('resize', update);

        window.addEventListener('scroll', function () {
            if (!ticking) { ticking = true; window.requestAnimationFrame(update); }
        }, { passive: true });
        update();
    })();
</script>
<script>
    // How it works: the line fills up to a reading line at 55% of the viewport;
    // every step whose dot has passed it lights up, the last one drives the screenshot.
    (function () {
        var rail = document.querySelector('[data-how]');
        if (!rail) return;
        var track = rail.querySelector('.wo-how__track');
        var fill = rail.querySelector('.wo-how__fill');
        var steps = Array.prototype.slice.call(rail.querySelectorAll('[data-how-step]'));
        var shots = Array.prototype.slice.call(document.querySelectorAll('[data-how-shot]'));
        var ticking = false;

        // run the track exactly from the first dot to the last one
        function measure() {
            var r = rail.getBoundingClientRect();
            var first = steps[0].querySelector('.wo-how__dot').getBoundingClientRect();
            var last = steps[steps.length - 1].querySelector('.wo-how__dot').getBoundingClientRect();
            track.style.top = (first.top + first.height / 2 - r.top) + 'px';
            track.style.bottom = (r.bottom - (last.top + last.height / 2)) + 'px';
        }

        function update() {
            ticking = false;
            var line = window.innerHeight * 0.55;
            var t = track.getBoundingClientRect();
            var p = Math.min(1, Math.max(0, (line - t.top) / t.height));
            fill.style.transform = 'scaleY(' + p.toFixed(4) + ')';

            var reachedLast = -1;
            steps.forEach(function (step, i) {
                var dot = step.querySelector('.wo-how__dot').getBoundingClientRect();
                var reached = dot.top + dot.height / 2 <= line;
                step.classList.toggle('is-reached', reached);
                // mobile: each step's own scene plays once its dot is reached
                var inline = step.querySelector('.wo-scene');
                if (inline) inline.classList.toggle('is-on', reached);
                if (reached) reachedLast = i;
            });
            var shown = Math.max(reachedLast, 0);
            steps.forEach(function (step, i) { step.classList.toggle('is-active', i === reachedLast); });
            // desktop: the stage shows the first scene at rest until step 1 is reached,
            // then shows and plays the current step's scene
            shots.forEach(function (shot, i) {
                shot.classList.toggle('is-active', i === shown);
                shot.classList.toggle('is-on', i === reachedLast);
            });
        }

        function request() {
            if (!ticking) { ticking = true; window.requestAnimationFrame(update); }
        }
        window.addEventListener('scroll', request, { passive: true });
        window.addEventListener('resize', function () { measure(); request(); });
        // thumbnails/fonts change the step heights after load
        window.addEventListener('load', function () { measure(); request(); });
        rail.classList.add('is-ready');
        measure();
        update();
    })();

    // Pricing: the team-size slider highlights the smallest package that fits.
    (function () {
        var box = document.querySelector('[data-pricing]');
        if (!box) return;
        var input = box.querySelector('input');
        var out = box.querySelector('output');
        var cards = Array.prototype.slice.call(document.querySelectorAll('.wo-plan'));
        var more = document.querySelector('[data-pricing-more]');
        var min = Number(input.min);
        var max = Number(input.max);

        function label(n) {
            if (n >= max) return box.dataset.over;
            return n === 1 ? box.dataset.one : box.dataset.many.replace(':count', n);
        }

        function update() {
            var n = Number(input.value);
            out.textContent = label(n);
            input.setAttribute('aria-valuetext', out.textContent);
            input.style.setProperty('--v', ((n - min) / Math.max(1, max - min) * 100) + '%');

            var fit = null;
            cards.forEach(function (card) {
                var limit = Number(card.dataset.limit);
                if (!fit && n < max && limit >= n) fit = card;
                card.classList.toggle('is-small', limit < n);
            });
            cards.forEach(function (card) { card.classList.toggle('is-fit', card === fit); });
            if (more) more.classList.toggle('is-active', !fit);
        }

        input.addEventListener('input', update);
        update();
    })();
</script>
@endpush
