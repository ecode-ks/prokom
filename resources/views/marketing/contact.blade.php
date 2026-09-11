@extends('marketing.layout')

@section('content')
<div class="wo-wrap">
    <section class="wo-section" style="padding-top:3rem">
        <div class="wo-contact-grid">
            <div>
                <span class="wo-label">{{ __('marketing.contact_label') }}</span>
                <h2>{{ __('marketing.contact_title') }}</h2>
                <p class="wo-lead">{{ __('marketing.contact_lead') }}</p>
            </div>

            <div class="wo-contact-links">
                <div class="wo-contact-links__intro">
                    <span class="wo-contact-links__eyebrow">{{ __('marketing.contact_channels') }}</span>
                    <p>{{ __('marketing.contact_follow') }}</p>
                </div>
                <div class="wo-contact-list">
                    <a class="wo-contact-link wo-contact-link--email" href="mailto:{{ __('marketing.contact_email_address') }}">
                        <span class="wo-contact-icon" aria-hidden="true"><i class="fa-solid fa-envelope"></i></span>
                        <span><small>{{ __('marketing.contact_email') }}</small><strong>{{ __('marketing.contact_email_address') }}</strong></span>
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
                <div class="wo-social-links" aria-label="{{ __('marketing.footer_social') }}">
                    <a href="{{ __('marketing.social_linkedin') }}" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
                    <a href="{{ __('marketing.social_instagram') }}" target="_blank" rel="noopener noreferrer" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                    <a href="{{ __('marketing.social_tiktok') }}" target="_blank" rel="noopener noreferrer" aria-label="TikTok"><i class="fa-brands fa-tiktok"></i></a>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
