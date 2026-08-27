@extends('marketing.layout')

@section('content')
<div class="wo-wrap">
    <section class="wo-module-hero wo-animate">
        <span class="wo-label wo-animate-item">{{ __('marketing.product_label') }}</span>
        <h1 class="wo-module-hero__title wo-animate-item">{{ __('marketing.product_title') }}</h1>
        <p class="wo-module-hero__lead wo-animate-item">{{ __('marketing.product_lead') }}</p>
        <p class="wo-module-hero__explain wo-animate-item">{{ __('marketing.product_explain') }}</p>
    </section>

    <section class="wo-section wo-animate" style="padding-top:3.5rem">
        <span class="wo-label wo-animate-item">{{ __('marketing.product_modules_label') }}</span>
        <h2 class="wo-animate-item">{{ __('marketing.product_modules_title') }}</h2>
        <p class="wo-lead wo-animate-item">{{ __('marketing.product_modules_lead') }}</p>

        <div class="wo-module-cards wo-module-cards--hub">
            <a href="{{ route('marketing.erp') }}" class="wo-module-card wo-animate-item">
                <span class="wo-module-card__tag">01</span>
                <h3>{{ __('marketing.nav_erp') }}</h3>
                <p>{{ __('marketing.erp_text') }}</p>
                <ul class="wo-module-card__points">
                    <li>{{ __('marketing.erp_f1_t') }}</li>
                    <li>{{ __('marketing.erp_f2_t') }}</li>
                    <li>{{ __('marketing.erp_f4_t') }}</li>
                </ul>
                <span class="wo-module-card-cta">{{ __('marketing.module_open') }} →</span>
            </a>
            <a href="{{ route('marketing.crm') }}" class="wo-module-card wo-animate-item">
                <span class="wo-module-card__tag">02</span>
                <h3>{{ __('marketing.nav_crm') }}</h3>
                <p>{{ __('marketing.crm_text') }}</p>
                <ul class="wo-module-card__points">
                    <li>{{ __('marketing.crm_f1_t') }}</li>
                    <li>{{ __('marketing.crm_f2_t') }}</li>
                    <li>{{ __('marketing.crm_f4_t') }}</li>
                </ul>
                <span class="wo-module-card-cta">{{ __('marketing.module_open') }} →</span>
            </a>
            <a href="{{ route('marketing.hr') }}" class="wo-module-card wo-animate-item">
                <span class="wo-module-card__tag">03</span>
                <h3>{{ __('marketing.nav_hr') }}</h3>
                <p>{{ __('marketing.hr_text') }}</p>
                <ul class="wo-module-card__points">
                    <li>{{ __('marketing.hr_f1_t') }}</li>
                    <li>{{ __('marketing.hr_f3_t') }}</li>
                    <li>{{ __('marketing.hr_f4_t') }}</li>
                </ul>
                <span class="wo-module-card-cta">{{ __('marketing.module_open') }} →</span>
            </a>
        </div>
    </section>

    <section class="wo-section wo-animate" style="padding-top:4rem">
        <span class="wo-label wo-animate-item">{{ __('marketing.product_flow_label') }}</span>
        <h2 class="wo-animate-item">{{ __('marketing.product_flow_title') }}</h2>
        <p class="wo-lead wo-animate-item">{{ __('marketing.product_flow_lead') }}</p>
        <ol class="wo-flow">
            <li class="wo-animate-item">
                <span class="wo-flow__num">1</span>
                <div>
                    <h3>{{ __('marketing.product_flow_1_t') }}</h3>
                    <p>{{ __('marketing.product_flow_1_d') }}</p>
                </div>
            </li>
            <li class="wo-animate-item">
                <span class="wo-flow__num">2</span>
                <div>
                    <h3>{{ __('marketing.product_flow_2_t') }}</h3>
                    <p>{{ __('marketing.product_flow_2_d') }}</p>
                </div>
            </li>
            <li class="wo-animate-item">
                <span class="wo-flow__num">3</span>
                <div>
                    <h3>{{ __('marketing.product_flow_3_t') }}</h3>
                    <p>{{ __('marketing.product_flow_3_d') }}</p>
                </div>
            </li>
        </ol>
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
