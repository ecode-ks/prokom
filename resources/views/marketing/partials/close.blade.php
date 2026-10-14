{{--
    Closing CTA, echoing the home hero. Its top edge is an arc in the colour of the section above:
    $above = 'tint' (--bg-2, default) or 'white'.
--}}
<section class="wo-close{{ ($above ?? 'tint') === 'white' ? ' wo-close--after-white' : '' }}" id="demo" aria-labelledby="wo-close-title">
    <div class="wo-wrap wo-close__inner">
        <span class="wo-label">{{ __('marketing.cta_label') }}</span>
        <h2 id="wo-close-title">{{ __('marketing.cta_title') }}</h2>
        <p class="wo-close__lead">{{ __('marketing.cta_lead') }}</p>
        <div class="wo-hero__actions">
            <a class="wo-btn wo-btn-primary" href="{{ route('marketing.contact') }}">{{ __('marketing.nav_demo') }}</a>
            <a class="wo-btn wo-btn-glass" href="https://wa.me/{{ __('marketing.phone_wa') }}" target="_blank" rel="noopener noreferrer">{{ __('marketing.whatsapp_cta') }}</a>
        </div>
    </div>
</section>
