@extends('marketing.layout')

@section('content')
<div class="wo-wrap">
    <section class="wo-hero wo-reveal">
        <div class="wo-hero-copy">
            <span class="wo-label">{{ __('marketing.hero_kicker') }}</span>
            <h1>
                {{ __('marketing.hero_title') }}
                <span class="soft">{{ __('marketing.hero_title_soft') }}</span>
            </h1>
            <p class="lead">{{ __('marketing.hero_lead') }}</p>
            <div class="wo-cta-row">
                <a class="wo-btn wo-btn-primary" href="{{ route('marketing.contact') }}">{{ __('marketing.hero_cta_demo') }}</a>
                <a class="wo-btn wo-btn-ghost" href="{{ route('marketing.product') }}">{{ __('marketing.hero_cta_product') }}</a>
            </div>
            <div class="wo-stack">{{ __('marketing.hero_stack') }}</div>
        </div>
        <div class="wo-hero-visual">
            @include('marketing.partials.shot', ['file' => 'dashboard.png', 'label' => __('marketing.shot_dashboard')])
        </div>
    </section>
</div>

<div class="wo-marquee-wrap">
    <div class="wo-marquee-label">{{ __('marketing.trusted_label') }}</div>
    <div class="wo-marquee" aria-label="{{ __('marketing.marquee') }}">
        <span>{{ __('marketing.marquee') }}</span>
        <span>{{ __('marketing.marquee') }}</span>
    </div>
</div>

<div class="wo-wrap">
    <section class="wo-section wo-split-section wo-animate">
        <div class="wo-split-copy wo-animate-item">
            <span class="wo-label">{{ __('marketing.who_label') }}</span>
            <h2>{{ __('marketing.who_title') }}</h2>
            <p class="wo-lead">{{ __('marketing.who_text') }}</p>
            <a class="wo-btn wo-btn-ghost" href="{{ route('marketing.product') }}">{{ __('marketing.nav_product') }}</a>
        </div>
        <div class="wo-split-cards">
            <article class="wo-card wo-animate-item">
                <span class="idx">01</span>
                <h3>{{ __('marketing.pillar_1_t') }}</h3>
                <p>{{ __('marketing.pillar_1_d') }}</p>
            </article>
            <article class="wo-card wo-animate-item">
                <span class="idx">02</span>
                <h3>{{ __('marketing.pillar_2_t') }}</h3>
                <p>{{ __('marketing.pillar_2_d') }}</p>
            </article>
            <article class="wo-card wo-animate-item">
                <span class="idx">03</span>
                <h3>{{ __('marketing.pillar_3_t') }}</h3>
                <p>{{ __('marketing.pillar_3_d') }}</p>
            </article>
            <article class="wo-card wo-animate-item">
                <span class="idx">04</span>
                <h3>{{ __('marketing.pillar_4_t') }}</h3>
                <p>{{ __('marketing.pillar_4_d') }}</p>
            </article>
        </div>
    </section>

    <section class="wo-section wo-animate">
        <span class="wo-label wo-animate-item">{{ __('marketing.how_label') }}</span>
        <h2 class="wo-animate-item">{{ __('marketing.how_title') }}</h2>
        <ol class="wo-steps">
            <li class="wo-animate-item">
                <span class="idx">01</span>
                <h3>{{ __('marketing.how_1_t') }}</h3>
                <p>{{ __('marketing.how_1') }}</p>
            </li>
            <li class="wo-animate-item">
                <span class="idx">02</span>
                <h3>{{ __('marketing.how_2_t') }}</h3>
                <p>{{ __('marketing.how_2') }}</p>
            </li>
            <li class="wo-animate-item">
                <span class="idx">03</span>
                <h3>{{ __('marketing.how_3_t') }}</h3>
                <p>{{ __('marketing.how_3') }}</p>
            </li>
            <li class="wo-animate-item">
                <span class="idx">04</span>
                <h3>{{ __('marketing.how_4_t') }}</h3>
                <p>{{ __('marketing.how_4') }}</p>
            </li>
        </ol>
    </section>

    <section class="wo-section wo-clients wo-animate">
        <span class="wo-label wo-animate-item">{{ __('marketing.clients_label') }}</span>
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
        <div class="wo-logos wo-logos-desktop wo-animate-item" aria-label="{{ __('marketing.clients_label') }}">
            @foreach ($clients as $client)
                @php $logoPath = public_path('images/marketing/' . $client['file']); @endphp
                @if (is_file($logoPath))
                    <img src="{{ asset('images/marketing/' . $client['file']) }}" alt="{{ $client['alt'] }}" loading="lazy">
                @endif
            @endforeach
        </div>
        <div class="wo-clients-slider wo-animate-item" aria-hidden="true">
            <div class="wo-clients-track">
                @foreach ([1, 2] as $loopSet)
                    @foreach ($clients as $client)
                        @php $logoPath = public_path('images/marketing/' . $client['file']); @endphp
                        @if (is_file($logoPath))
                            <img src="{{ asset('images/marketing/' . $client['file']) }}" alt="" loading="lazy">
                        @endif
                    @endforeach
                @endforeach
            </div>
        </div>
    </section>

    <section class="wo-section wo-animate">
        <span class="wo-label wo-animate-item">{{ __('marketing.pricing_label') }}</span>
        <h2 class="wo-animate-item">{{ __('marketing.pricing_title') }}</h2>
        <p class="wo-lead wo-animate-item">{{ __('marketing.pricing_lead') }}</p>
        <div class="wo-prices-wrap">
            <div class="wo-prices" id="woPrices" tabindex="0" aria-label="{{ __('marketing.pricing_title') }}">
                <article class="wo-price wo-animate-item">
                    <div class="wo-price-top">
                        <h3>{{ __('marketing.price_trial') }}</h3>
                        <span class="wo-price-tag">{{ __('marketing.price_trial_tag') }}</span>
                    </div>
                    <p>{{ __('marketing.price_trial_d') }}</p>
                    <ul class="wo-price-list">
                        <li>{{ __('marketing.price_trial_f1') }}</li>
                        <li>{{ __('marketing.price_trial_f2') }}</li>
                        <li>{{ __('marketing.price_trial_f3') }}</li>
                        <li>{{ __('marketing.price_trial_f4') }}</li>
                        <li>{{ __('marketing.price_trial_f5') }}</li>
                    </ul>
                    <a class="wo-btn wo-btn-ghost" href="{{ route('marketing.contact') }}">{{ __('marketing.price_cta') }}</a>
                </article>
                <article class="wo-price is-featured wo-animate-item">
                    <span class="wo-price-badge">{{ __('marketing.price_std_badge') }}</span>
                    <div class="wo-price-top">
                        <h3>{{ __('marketing.price_std') }}</h3>
                        <span class="wo-price-tag">{{ __('marketing.price_std_tag') }}</span>
                    </div>
                    <p>{{ __('marketing.price_std_d') }}</p>
                    <ul class="wo-price-list">
                        <li>{{ __('marketing.price_std_f1') }}</li>
                        <li>{{ __('marketing.price_std_f2') }}</li>
                        <li>{{ __('marketing.price_std_f3') }}</li>
                        <li>{{ __('marketing.price_std_f4') }}</li>
                        <li>{{ __('marketing.price_std_f5') }}</li>
                        <li>{{ __('marketing.price_std_f6') }}</li>
                    </ul>
                    <a class="wo-btn wo-btn-primary" href="{{ route('marketing.contact') }}">{{ __('marketing.price_cta') }}</a>
                </article>
                <article class="wo-price wo-animate-item">
                    <div class="wo-price-top">
                        <h3>{{ __('marketing.price_pro') }}</h3>
                        <span class="wo-price-tag">{{ __('marketing.price_pro_tag') }}</span>
                    </div>
                    <p>{{ __('marketing.price_pro_d') }}</p>
                    <ul class="wo-price-list">
                        <li>{{ __('marketing.price_pro_f1') }}</li>
                        <li>{{ __('marketing.price_pro_f2') }}</li>
                        <li>{{ __('marketing.price_pro_f3') }}</li>
                        <li>{{ __('marketing.price_pro_f4') }}</li>
                        <li>{{ __('marketing.price_pro_f5') }}</li>
                        <li>{{ __('marketing.price_pro_f6') }}</li>
                    </ul>
                    <a class="wo-btn wo-btn-ghost" href="{{ route('marketing.contact') }}">{{ __('marketing.price_cta') }}</a>
                </article>
            </div>
        </div>
    </section>

    <section class="wo-band wo-animate">
        <span class="wo-label wo-animate-item">{{ __('marketing.contact_label') }}</span>
        <h2 class="wo-animate-item">{{ __('marketing.contact_title') }}</h2>
        <p class="wo-lead wo-animate-item">{{ __('marketing.contact_lead') }}</p>
        <div class="wo-cta-row wo-animate-item">
            <a class="wo-btn wo-btn-primary" href="{{ route('marketing.contact') }}">{{ __('marketing.nav_demo') }}</a>
            <a class="wo-btn wo-btn-ghost" href="https://wa.me/{{ __('marketing.phone_wa') }}" target="_blank" rel="noopener noreferrer">{{ __('marketing.whatsapp_cta') }}</a>
        </div>
    </section>
</div>
@endsection
