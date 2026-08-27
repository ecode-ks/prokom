@extends('marketing.layout')

@section('content')
<div class="wo-wrap container">
    <section class="wo-section" style="padding-top:3rem">
        <div class="row g-4 align-items-start">
            <div class="col-12 col-lg-5">
                <span class="wo-label">{{ __('marketing.contact_label') }}</span>
                <h2>{{ __('marketing.contact_title') }}</h2>
                <p class="wo-lead">{{ __('marketing.contact_lead') }}</p>
            </div>

            <div class="col-12 col-lg-7">
                <div class="wo-contact-links">
                    <div class="wo-contact-links__intro">
                    <span class="wo-contact-links__eyebrow">{{ __('marketing.contact_channels') }}</span>
                    <p>{{ __('marketing.contact_follow') }}</p>
                    </div>
                    <div class="wo-contact-list">
                    <a class="wo-contact-link wo-contact-link--email" href="mailto:{{ config('mail.from.address') }}">
                        <span class="wo-contact-icon" aria-hidden="true"><i class="fa-solid fa-envelope"></i></span>
                        <span><small>{{ __('marketing.contact_email') }}</small><strong>{{ config('mail.from.address') }}</strong></span>
                        <span class="wo-contact-arrow" aria-hidden="true">↗</span>
                    </a>
                    <a class="wo-contact-link wo-contact-link--phone" href="tel:{{ __('marketing.phone_wa') }}">
                        <span class="wo-contact-icon" aria-hidden="true"><i class="fa-solid fa-phone"></i></span>
                        <span><small>{{ __('marketing.contact_phone') }}</small><strong>{{ __('marketing.phone_display') }}</strong></span>
                        <span class="wo-contact-arrow" aria-hidden="true">↗</span>
                    </a>
                    <a class="wo-contact-link wo-contact-link--whatsapp" href="https://wa.me/{{ __('marketing.phone_wa') }}" target="_blank" rel="noopener noreferrer">
                        <span class="wo-contact-icon" aria-hidden="true"><i class="fa-brands fa-whatsapp"></i></span>
                        <span><small>{{ __('marketing.whatsapp') }}</small><strong>{{ __('marketing.whatsapp_cta') }}</strong></span>
                        <span class="wo-contact-arrow" aria-hidden="true">↗</span>
                    </a>
                    </div>
                    <div class="wo-social-links">
                    <a href="https://www.linkedin.com/company/workom/" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
                    <a href="https://www.facebook.com/workom" target="_blank" rel="noopener noreferrer" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="https://www.instagram.com/workom/" target="_blank" rel="noopener noreferrer" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                    <a href="https://www.tiktok.com/@workom" target="_blank" rel="noopener noreferrer" aria-label="TikTok"><i class="fa-brands fa-tiktok"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
