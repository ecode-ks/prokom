@extends('marketing.layout')

@section('content')
<div class="wo-wrap">
    <section class="wo-section wo-animate" style="padding-top:3rem">
        <span class="wo-label wo-animate-item">{{ __('marketing.product_label') }}</span>
        <h2 class="wo-animate-item">{{ __('marketing.product_title') }}</h2>
        <p class="wo-lead wo-animate-item">{{ __('marketing.product_lead') }}</p>

        <div class="wo-shot-grid wo-animate-item">
            @include('marketing.partials.shot', ['file' => 'dashboard.png', 'label' => __('marketing.shot_dashboard')])
            @include('marketing.partials.shot', ['file' => 'projects.png', 'label' => __('marketing.shot_projects')])
            @include('marketing.partials.shot', ['file' => 'offers.png', 'label' => __('marketing.shot_offers')])
        </div>

        <div class="wo-feature wo-animate-item">
            <div class="wo-feature-copy">
                <span class="idx">01</span>
                <h3>{{ __('marketing.erp_title') }}</h3>
                <p>{{ __('marketing.erp_text') }}</p>
                <ul>
                    <li>{{ __('marketing.erp_i1') }}</li>
                    <li>{{ __('marketing.erp_i2') }}</li>
                    <li>{{ __('marketing.erp_i3') }}</li>
                    <li>{{ __('marketing.erp_i4') }}</li>
                </ul>
            </div>
            @include('marketing.partials.shot', ['file' => 'checkin.png', 'label' => __('marketing.shot_checkin')])
        </div>

        <div class="wo-feature wo-animate-item">
            <div class="wo-feature-copy">
                <span class="idx">02</span>
                <h3>{{ __('marketing.crm_title') }}</h3>
                <p>{{ __('marketing.crm_text') }}</p>
                <ul>
                    <li>{{ __('marketing.crm_i1') }}</li>
                    <li>{{ __('marketing.crm_i2') }}</li>
                    <li>{{ __('marketing.crm_i3') }}</li>
                    <li>{{ __('marketing.crm_i4') }}</li>
                </ul>
            </div>
            @include('marketing.partials.shot', ['file' => 'offers.png', 'label' => __('marketing.shot_offers')])
        </div>

        <div class="wo-feature wo-animate-item">
            <div class="wo-feature-copy">
                <span class="idx">03</span>
                <h3>{{ __('marketing.hr_title') }}</h3>
                <p>{{ __('marketing.hr_text') }}</p>
                <ul>
                    <li>{{ __('marketing.hr_i1') }}</li>
                    <li>{{ __('marketing.hr_i2') }}</li>
                    <li>{{ __('marketing.hr_i3') }}</li>
                    <li>{{ __('marketing.hr_i4') }}</li>
                </ul>
            </div>
            @include('marketing.partials.shot', ['file' => 'staff.png', 'label' => __('marketing.shot_staff')])
        </div>
    </section>

    <section class="wo-band wo-animate">
        <h2 class="wo-animate-item">{{ __('marketing.contact_title') }}</h2>
        <p class="wo-lead wo-animate-item">{{ __('marketing.contact_lead') }}</p>
        <a class="wo-btn wo-btn-primary wo-animate-item" href="{{ route('marketing.contact') }}">{{ __('marketing.nav_demo') }}</a>
    </section>
</div>
@endsection
