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

<div class="wo-wrap">
    <section class="wo-section wo-animate" id="per-ke-eshte">
        <span class="wo-label wo-animate-item">{{ __('marketing.who_label') }}</span>
        <h2 class="wo-animate-item">{{ __('marketing.who_title') }}</h2>
        <p class="wo-lead wo-animate-item">{{ __('marketing.who_text') }}</p>

        <div class="wo-cards wo-cards--swipe wo-animate-item" style="margin-top:2rem">
            @foreach (['erp', 'crm', 'hr'] as $mod)
                @include('marketing.partials.module-card', ['mod' => $mod])
            @endforeach
        </div>
    </section>

</div>

{{-- How it works: scrollytelling. A line fills as the visitor scrolls; each step plays its own illustrated scene. --}}
{{-- Desktop: the stage sticks on the left; the intro starts on the same line beside it and the steps follow. --}}
<section class="wo-how" id="si-funksionon" aria-labelledby="wo-how-title">
    <div class="wo-wrap">
        <div class="wo-how__grid">
        <div class="wo-how__stage" aria-hidden="true">
            @foreach (range(1, 5) as $n)
                @include('marketing.partials.how-scene', ['n' => $n, 'shot' => $n - 1])
            @endforeach
        </div>

        <div class="wo-how__side">
        <div class="wo-how__intro">
            <span class="wo-label">{{ __('marketing.how_label') }}</span>
            <h2 id="wo-how-title">{{ __('marketing.how_title') }}</h2>
            <p class="wo-lead">{{ __('marketing.how_lead') }}</p>
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
    </div>
</section>

{{-- Client logos, right before the prices --}}
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

{{-- Pricing: template packages (config/marketing.php for prices, lang 'pricing_plans' for names and
     features), not the packages table. A toggle switches between monthly and yearly prices. --}}
@php
    $locale = app()->getLocale();
    $money = function ($amount) use ($locale) {
        $amount = (float) $amount;
        $decimals = fmod($amount, 1.0) == 0.0 ? 0 : 2;
        return $locale === 'en'
            ? '€' . number_format($amount, $decimals, '.', ',')
            : number_format($amount, $decimals, ',', '.') . ' €';
    };
    $plans = config('marketing.plans', []);
    $texts = __('marketing.pricing_plans');
    $maxStaff = (int) collect($plans)->max('staff');
    $periods = ['yearly' => 12, 'monthly' => 1];
    $shown = 'yearly';
    // best yearly saving against paying the same plan monthly
    $savePct = (int) collect($plans)
        ->map(fn ($plan) => $plan['monthly'] > 0 ? round(100 - $plan['yearly'] / ($plan['monthly'] * 12) * 100) : 0)
        ->max();
@endphp
<section class="wo-pricing" id="cmimet" aria-labelledby="wo-pricing-title">
    <div class="wo-wrap">
        <div class="wo-pricing__head">
            <span class="wo-label">{{ __('marketing.pricing_label') }}</span>
            <h2 id="wo-pricing-title">{{ __('marketing.pricing_title') }}</h2>
            <p class="wo-lead">{{ __('marketing.pricing_lead') }}</p>
        </div>

        <div class="wo-billing" role="group" aria-label="{{ __('marketing.pricing_billing') }}" data-billing>
            <div class="wo-billing__seg">
                @foreach (['monthly', 'yearly'] as $period)
                    <button type="button" data-billing-for="{{ $period }}" aria-pressed="{{ $period === $shown ? 'true' : 'false' }}">{{ __('marketing.pricing_' . $period) }}</button>
                @endforeach
            </div>
            @if ($savePct > 0)
                <span class="wo-billing__save">{{ __('marketing.pricing_save', ['pct' => $savePct]) }}</span>
            @endif
        </div>

        @foreach ($periods as $period => $months)
            <ul class="wo-pricing__cards" role="list" data-billing-group="{{ $period }}" @if ($period !== $shown) hidden @endif>
                @foreach ($plans as $key => $plan)
                    @php $popular = ! empty($plan['popular']); @endphp
                    <li class="wo-plan{{ $popular ? ' wo-plan--pop' : '' }}">
                        @if ($popular)
                            <span class="wo-plan__badge">{{ __('marketing.pricing_popular') }}</span>
                        @endif
                        <h3 class="wo-plan__title">{{ $texts[$key]['name'] ?? ucfirst($key) }}</h3>
                        <p class="wo-plan__staff">{{ __('marketing.pricing_staff', ['count' => $plan['staff']]) }}</p>
                        <p class="wo-plan__price">
                            <strong>{{ $money($plan[$period]) }}</strong>
                            <span>{{ __('marketing.pricing_unit_' . ($months === 12 ? 'year' : 'month')) }}</span>
                        </p>
                        @if ($months === 12)
                            <p class="wo-plan__monthly">{{ __('marketing.pricing_per_month', ['price' => $money(round($plan['yearly'] / 12, 2))]) }}</p>
                        @endif
                        @if (! empty($texts[$key]['features']))
                            <ul class="wo-plan__features">
                                @foreach ($texts[$key]['features'] as $feature)
                                    <li>{{ $feature }}</li>
                                @endforeach
                            </ul>
                        @endif
                        <a class="wo-btn {{ $popular ? 'wo-btn-primary' : 'wo-btn-ghost' }} wo-plan__cta" href="{{ route('register') }}">{{ __('marketing.pricing_cta') }}</a>
                    </li>
                @endforeach
            </ul>
        @endforeach

        <p class="wo-pricing__more">
            <span>{{ __('marketing.pricing_more', ['count' => $maxStaff]) }}</span>
            <a href="{{ route('marketing.contact', ['intent' => 'pricing']) }}">{{ __('marketing.pricing_more_cta') }} →</a>
        </p>
    </div>
</section>

@include('marketing.partials.close')
@endsection

@push('scripts')
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

    // Pricing: the billing toggle shows one group of packages at a time.
    (function () {
        var box = document.querySelector('[data-billing]');
        if (!box) return;
        var buttons = Array.prototype.slice.call(box.querySelectorAll('[data-billing-for]'));
        var groups = Array.prototype.slice.call(document.querySelectorAll('[data-billing-group]'));
        // phones: the cards are a sideways row; open it on the most popular card, in the middle
        function centerPopular(group) {
            var pop = group.querySelector('.wo-plan--pop');
            if (!pop || group.scrollWidth <= group.clientWidth) return;
            var g = group.getBoundingClientRect();
            var p = pop.getBoundingClientRect();
            group.scrollLeft += (p.left - g.left) - (g.width - p.width) / 2;
        }

        buttons.forEach(function (button) {
            button.addEventListener('click', function () {
                var months = button.getAttribute('data-billing-for');
                buttons.forEach(function (b) { b.setAttribute('aria-pressed', b === button ? 'true' : 'false'); });
                groups.forEach(function (g) {
                    g.hidden = g.getAttribute('data-billing-group') !== months;
                    if (!g.hidden) centerPopular(g);
                });
            });
        });
        groups.forEach(function (g) { if (!g.hidden) centerPopular(g); });
    })();
</script>
@endpush
