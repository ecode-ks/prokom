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

{{-- Client logos, right before the free trial. On phones they run as an endless slider:
     the list is drawn twice and the track moves by one list's width, then starts over. --}}
@php
    $clients = array_filter($clients, fn ($client) => is_file(public_path('images/marketing/' . $client['file'])));
@endphp
<section class="wo-wrap wo-trust" id="klientet" aria-labelledby="wo-trust-label">
    <p class="wo-trust__label" id="wo-trust-label">{{ __('marketing.clients_label') }}</p>
    <div class="wo-trust__marquee">
        <div class="wo-trust__track">
            @foreach ([false, true] as $clone)
                <ul class="wo-trust__logos{{ $clone ? ' wo-trust__logos--clone' : '' }}" @if ($clone) aria-hidden="true" @endif>
                    @foreach ($clients as $client)
                        <li><img src="{{ asset('images/marketing/' . $client['file']) }}" alt="{{ $clone ? '' : $client['alt'] }}" loading="lazy" decoding="async"></li>
                    @endforeach
                </ul>
            @endforeach
        </div>
    </div>
</section>

<section class="wo-trial" id="prova-falas" aria-labelledby="wo-trial-title">
    <div class="wo-wrap wo-trial__inner">
        <div class="wo-trial__copy">
            <h2 id="wo-trial-title">{{ __('marketing.trial_title') }} <span>{{ __('marketing.trial_offer') }}</span></h2>
            <p class="wo-trial__lead">{{ __('marketing.trial_lead') }}</p>
            <a class="wo-btn wo-btn-primary wo-trial__cta" href="{{ route('register') }}">
                {{ __('marketing.trial_cta') }}
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6"/></svg>
            </a>
            <p class="wo-trial__note">{{ __('marketing.trial_note') }}</p>
        </div>
        <div class="wo-trial__visual" aria-hidden="true">
            <div class="wo-trial__calendar">
                <div class="wo-trial__calendar-top"><span>WORKOM</span><span>ERP / CRM / HR</span></div>
                <div class="wo-trial__calendar-body">
                    <span class="wo-trial__one">1</span>
                    <span class="wo-trial__month">{{ __('marketing.trial_month') }}</span>
                </div>
                <div class="wo-trial__calendar-bottom">
                    <span>{{ __('marketing.trial_calendar_note') }}</span>
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 4 4L19 6"/></svg>
                </div>
            </div>
        </div>
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

</script>
@endpush
