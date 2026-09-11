@extends('marketing.layout')

@section('content')
@php
    $prefix = $module;
    $features = [
        ['t' => __('marketing.' . $prefix . '_f1_t'), 'd' => __('marketing.' . $prefix . '_f1_d')],
        ['t' => __('marketing.' . $prefix . '_f2_t'), 'd' => __('marketing.' . $prefix . '_f2_d')],
        ['t' => __('marketing.' . $prefix . '_f3_t'), 'd' => __('marketing.' . $prefix . '_f3_d')],
        ['t' => __('marketing.' . $prefix . '_f4_t'), 'd' => __('marketing.' . $prefix . '_f4_d')],
        ['t' => __('marketing.' . $prefix . '_f5_t'), 'd' => __('marketing.' . $prefix . '_f5_d')],
    ];
@endphp
<div class="wo-wrap">
    <section class="wo-module-hero wo-animate">
        <a href="{{ route('marketing.product') }}" class="wo-back-link wo-animate-item">
            ← {{ __('marketing.nav_product') }}
        </a>
        <span class="wo-label wo-animate-item">{{ __('marketing.nav_' . $prefix) }}</span>
        <h1 class="wo-module-hero__title wo-animate-item">{{ __('marketing.' . $prefix . '_title') }}</h1>
        <p class="wo-module-hero__lead wo-animate-item">{{ __('marketing.' . $prefix . '_page_lead') }}</p>
        <p class="wo-module-hero__explain wo-animate-item">{{ __('marketing.' . $prefix . '_explain') }}</p>
    </section>

    <section class="wo-section wo-animate" style="padding-top:4rem">
        <span class="wo-label wo-animate-item">{{ __('marketing.module_includes') }}</span>
        <h2 class="wo-animate-item">{{ __('marketing.module_includes_title') }}</h2>
        <p class="wo-lead wo-animate-item">{{ __('marketing.' . $prefix . '_includes_lead') }}</p>

        <div class="wo-feature-grid">
            @foreach ($features as $i => $feature)
                <article class="wo-feature-card wo-animate-item">
                    <span class="wo-feature-card__num">{{ str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) }}</span>
                    <h3>{{ $feature['t'] }}</h3>
                    <p>{{ $feature['d'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    @if (!empty($shots))
        <section class="wo-section wo-animate" id="pamjet" style="padding-top:4rem">
            <span class="wo-label wo-animate-item">{{ __('marketing.module_shots_label') }}</span>
            <h2 class="wo-animate-item">{{ __('marketing.module_shots_title') }}</h2>
            <p class="wo-lead wo-animate-item">{{ __('marketing.module_shots_lead') }}</p>
            <div class="wo-shot-grid wo-animate-item">
                @foreach ($shots as $shot)
                    @php
                        $file = $shot['file'];
                        $label = $shot['label'];
                        $caption = $shot['caption'] ?? null;
                        $path = public_path('images/marketing/' . $file);
                        $hasImage = is_file($path);
                        $src = $hasImage ? asset('images/marketing/' . $file) : null;
                        $placeholder = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='10'%3E%3Crect width='16' height='10' fill='%23e8eef6'/%3E%3C/svg%3E";
                    @endphp
                    <figure class="wo-shot">
                        <div class="wo-shot-body">
                            @if($hasImage)
                                <button
                                    type="button"
                                    class="wo-shot-open"
                                    data-wo-lightbox="{{ $src }}"
                                    data-wo-lightbox-label="{{ $label }}"
                                    aria-label="{{ $label }}"
                                >
                                    <img
                                        class="wo-lazy"
                                        src="{{ $placeholder }}"
                                        data-wo-src="{{ $src }}"
                                        alt="{{ $label }} — WORKOM"
                                        loading="lazy"
                                        decoding="async"
                                    >
                                </button>
                            @else
                                <div class="wo-shot-empty">
                                    <strong>{{ __('marketing.shot_placeholder') }}</strong>
                                    <div>{{ $label }}</div>
                                    <small>{{ __('marketing.shot_hint') }}{{ $file }}</small>
                                </div>
                            @endif
                        </div>
                        @if($label || $caption)
                            <figcaption class="wo-shot-caption">
                                @if($label)
                                    <strong>{{ $label }}</strong>
                                @endif
                                @if($caption)
                                    <p>{{ $caption }}</p>
                                @endif
                            </figcaption>
                        @endif
                    </figure>
                @endforeach
            </div>
        </section>
    @endif

    <section class="wo-module-more wo-animate">
        <span class="wo-label wo-animate-item">{{ __('marketing.module_also') }}</span>
        <h2 class="wo-animate-item" style="max-width:20ch">{{ __('marketing.module_also_title') }}</h2>
        <div class="wo-module-cards wo-animate-item" style="margin-top:1.5rem">
            @if ($module !== 'erp')
                <a href="{{ route('marketing.erp') }}" class="wo-module-card">
                    <span class="wo-module-card__tag">01</span>
                    <h3>{{ __('marketing.nav_erp') }}</h3>
                    <p>{{ __('marketing.erp_text') }}</p>
                    <span class="wo-module-card-cta">{{ __('marketing.module_open') }} →</span>
                </a>
            @endif
            @if ($module !== 'crm')
                <a href="{{ route('marketing.crm') }}" class="wo-module-card">
                    <span class="wo-module-card__tag">02</span>
                    <h3>{{ __('marketing.nav_crm') }}</h3>
                    <p>{{ __('marketing.crm_text') }}</p>
                    <span class="wo-module-card-cta">{{ __('marketing.module_open') }} →</span>
                </a>
            @endif
            @if ($module !== 'hr')
                <a href="{{ route('marketing.hr') }}" class="wo-module-card">
                    <span class="wo-module-card__tag">03</span>
                    <h3>{{ __('marketing.nav_hr') }}</h3>
                    <p>{{ __('marketing.hr_text') }}</p>
                    <span class="wo-module-card-cta">{{ __('marketing.module_open') }} →</span>
                </a>
            @endif
        </div>
    </section>

    <section class="wo-section wo-band wo-animate" id="demo" style="margin-top:4rem">
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
