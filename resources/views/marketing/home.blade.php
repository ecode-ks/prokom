@extends('marketing.layout')

@section('content')
<div class="wo-wrap">
    <section class="wo-hero wo-reveal">
        <div class="wo-hero-copy">
            <p class="wo-brand-mark">WORKOM</p>
            <h1>{{ __('marketing.hero_title') }}</h1>
            <p class="lead">{{ __('marketing.hero_lead') }}</p>
            <div class="wo-cta-row">
                <a class="wo-btn wo-btn-primary" href="{{ route('marketing.contact') }}">{{ __('marketing.hero_cta_demo') }}</a>
                <a class="wo-btn wo-btn-ghost" href="{{ route('marketing.product') }}">{{ __('marketing.hero_cta_product') }}</a>
            </div>
        </div>
        <div class="wo-hero-visual">
            @php
                $heroPh = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='10'%3E%3Crect width='16' height='10' fill='%23e8eef6'/%3E%3C/svg%3E";
            @endphp
            <div class="wo-hero-mosaic">
                <figure class="wo-shot wo-hero-mosaic__main">
                    <div class="wo-shot-body">
                        <button type="button" class="wo-shot-open" data-wo-lightbox="{{ asset('images/marketing/hero_site.jpg') }}" data-wo-lightbox-label="{{ __('marketing.hero_img_site') }}" aria-label="{{ __('marketing.hero_img_site') }}">
                            <img class="wo-lazy" src="{{ $heroPh }}" data-wo-src="{{ asset('images/marketing/hero_site.jpg') }}" alt="{{ __('marketing.hero_img_site') }}" loading="lazy" decoding="async">
                        </button>
                    </div>
                </figure>
                <figure class="wo-shot wo-hero-mosaic__side wo-hero-mosaic__side--a">
                    <div class="wo-shot-body">
                        <button type="button" class="wo-shot-open" data-wo-lightbox="{{ asset('images/marketing/hero_office.jpg') }}" data-wo-lightbox-label="{{ __('marketing.hero_img_office') }}" aria-label="{{ __('marketing.hero_img_office') }}">
                            <img class="wo-lazy" src="{{ $heroPh }}" data-wo-src="{{ asset('images/marketing/hero_office.jpg') }}" alt="{{ __('marketing.hero_img_office') }}" loading="lazy" decoding="async">
                        </button>
                    </div>
                </figure>
                <figure class="wo-shot wo-hero-mosaic__side wo-hero-mosaic__side--b">
                    <div class="wo-shot-body">
                        <button type="button" class="wo-shot-open" data-wo-lightbox="{{ asset('images/marketing/hero_team.jpg') }}" data-wo-lightbox-label="{{ __('marketing.hero_img_team') }}" aria-label="{{ __('marketing.hero_img_team') }}">
                            <img class="wo-lazy" src="{{ $heroPh }}" data-wo-src="{{ asset('images/marketing/hero_team.jpg') }}" alt="{{ __('marketing.hero_img_team') }}" loading="lazy" decoding="async">
                        </button>
                    </div>
                </figure>
            </div>
        </div>
    </section>
</div>

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

    <section class="wo-section wo-animate" id="si-funksionon">
        <span class="wo-label wo-animate-item">{{ __('marketing.how_label') }}</span>
        <h2 class="wo-animate-item">{{ __('marketing.how_title') }}</h2>
        <div class="wo-module-cards wo-animate-item" style="margin-top:2rem">
            <article class="wo-module-card">
                <h3>{{ __('marketing.how_1_t') }}</h3>
                <p>{{ __('marketing.how_1') }}</p>
            </article>
            <article class="wo-module-card">
                <h3>{{ __('marketing.how_2_t') }}</h3>
                <p>{{ __('marketing.how_2') }}</p>
            </article>
            <article class="wo-module-card">
                <h3>{{ __('marketing.how_3_t') }}</h3>
                <p>{{ __('marketing.how_3') }}</p>
            </article>
        </div>
    </section>

    <section class="wo-section wo-clients wo-animate" id="klientet">
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
        <div class="wo-logos-desktop wo-animate-item" aria-label="{{ __('marketing.clients_label') }}">
            <div class="wo-client-row">
                @foreach (array_slice($clients, 0, 4) as $client)
                    @php $logoPath = public_path('images/marketing/' . $client['file']); @endphp
                    @if (is_file($logoPath))
                        <div class="wo-client-card">
                            <img
                                class="wo-lazy"
                                src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='4'%3E%3Crect width='8' height='4' fill='%23071838'/%3E%3C/svg%3E"
                                data-wo-src="{{ asset('images/marketing/' . $client['file']) }}"
                                alt="{{ $client['alt'] }}"
                                loading="lazy"
                                decoding="async"
                            >
                        </div>
                    @endif
                @endforeach
            </div>
            <div class="wo-client-row">
                @foreach (array_slice($clients, 4) as $client)
                    @php $logoPath = public_path('images/marketing/' . $client['file']); @endphp
                    @if (is_file($logoPath))
                        <div class="wo-client-card">
                            <img
                                class="wo-lazy"
                                src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='4'%3E%3Crect width='8' height='4' fill='%23071838'/%3E%3C/svg%3E"
                                data-wo-src="{{ asset('images/marketing/' . $client['file']) }}"
                                alt="{{ $client['alt'] }}"
                                loading="lazy"
                                decoding="async"
                            >
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
        <div class="wo-clients-slider wo-animate-item" aria-hidden="true">
            <div class="wo-clients-track">
                @foreach ([1, 2] as $loopSet)
                    @foreach ($clients as $client)
                        @php $logoPath = public_path('images/marketing/' . $client['file']); @endphp
                        @if (is_file($logoPath))
                            <div class="wo-client-card">
                                <img
                                    class="wo-lazy"
                                    src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='8' height='4'%3E%3Crect width='8' height='4' fill='%23071838'/%3E%3C/svg%3E"
                                    data-wo-src="{{ asset('images/marketing/' . $client['file']) }}"
                                    alt=""
                                    loading="lazy"
                                    decoding="async"
                                >
                            </div>
                        @endif
                    @endforeach
                @endforeach
            </div>
        </div>
    </section>

    <section class="wo-section wo-band wo-animate" id="demo">
        <span class="wo-label wo-animate-item">{{ __('marketing.cta_label') }}</span>
        <h2 class="wo-animate-item">{{ __('marketing.cta_title') }}</h2>
        <p class="wo-lead wo-animate-item">{{ __('marketing.cta_lead') }}</p>
        <div class="wo-cta-row wo-animate-item">
            <a class="wo-btn wo-btn-primary" href="{{ route('marketing.contact') }}">{{ __('marketing.nav_demo') }}</a>
            <a class="wo-btn wo-btn-ghost" href="https://wa.me/{{ __('marketing.phone_wa') }}" target="_blank" rel="noopener noreferrer">{{ __('marketing.whatsapp_cta') }}</a>
        </div>
    </section>
</div>
@endsection
