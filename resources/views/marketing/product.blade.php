@extends('marketing.layout')

@section('content')
@php
    $modules = [
        'erp' => ['erp_f1_t', 'erp_f2_t', 'erp_f4_t'],
        'crm' => ['crm_f1_t', 'crm_f2_t', 'crm_f4_t'],
        'hr' => ['hr_f1_t', 'hr_f3_t', 'hr_f4_t'],
    ];
    // the example flow passes through the modules in this order
    $flow = [1 => 'crm', 2 => 'erp', 3 => 'hr'];
@endphp

<header class="wo-phead wo-phead--split">
    <div class="wo-wrap wo-phead__grid">
        <div class="wo-phead__copy">
            <span class="wo-label">{{ __('marketing.product_label') }}</span>
            <h1>{{ __('marketing.product_title') }}</h1>
            <p class="wo-phead__lead">{{ __('marketing.product_lead') }}</p>
            <p class="wo-phead__explain">{{ __('marketing.product_explain') }}</p>
            <div class="wo-cta-row">
                {{-- the demo request lives in the header and the closing section --}}
                <a class="wo-btn wo-btn-primary" href="#modulet">{{ __('marketing.product_cta_modules') }}</a>
                <a class="wo-btn wo-btn-ghost" href="{{ route('home') }}#cmimet">{{ __('marketing.product_cta_pricing') }}</a>
            </div>
        </div>
        <div class="wo-phead__art">
            @include('marketing.partials.product-map')
        </div>
    </div>
</header>

<section class="wo-wrap wo-section" id="modulet" aria-labelledby="wo-modules-title">
    <div class="wo-splithead">
        <div>
            <span class="wo-label">{{ __('marketing.product_modules_label') }}</span>
            <h2 id="wo-modules-title">{{ __('marketing.product_modules_title') }}</h2>
        </div>
        <p class="wo-lead">{{ __('marketing.product_modules_lead') }}</p>
    </div>
    <div class="wo-cards wo-cards--swipe">
        @foreach ($modules as $mod => $points)
            @include('marketing.partials.module-card', ['mod' => $mod, 'points' => $points])
        @endforeach
    </div>
</section>

<section class="wo-wrap wo-section wo-flowsec" id="rrjedha" aria-labelledby="wo-flow-title">
    <div class="wo-splithead">
        <div>
            <span class="wo-label">{{ __('marketing.product_flow_label') }}</span>
            <h2 id="wo-flow-title">{{ __('marketing.product_flow_title') }}</h2>
        </div>
        <p class="wo-lead">{{ __('marketing.product_flow_lead') }}</p>
    </div>
    {{-- a process diagram: three arrows that hand on to each other, light to dark (CRM → ERP → HR);
         they slide in one after another when the section scrolls into view --}}
    <ol class="wo-chain wo-animate">
        @foreach ($flow as $n => $mod)
            <li class="wo-chain__step">
                <div class="wo-chain__arrow">
                    <span class="wo-chain__icon" aria-hidden="true">@include('marketing.partials.module-icon', ['mod' => $mod])</span>
                    <span class="wo-chain__mod"><small>{{ str_pad($n, 2, '0', STR_PAD_LEFT) }}</small>{{ __('marketing.nav_' . $mod) }}</span>
                </div>
                <div class="wo-chain__body">
                    <h3>{{ __('marketing.product_flow_' . $n . '_t') }}</h3>
                    <p>{{ __('marketing.product_flow_' . $n . '_d') }}</p>
                    {{-- what this step hands on to the next module --}}
                    <p class="wo-chain__out">{{ __('marketing.product_flow_' . $n . '_out') }}</p>
                </div>
            </li>
        @endforeach
    </ol>
</section>

@include('marketing.partials.close', ['above' => 'white'])
@endsection
