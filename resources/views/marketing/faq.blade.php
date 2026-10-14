@extends('marketing.layout')

@push('head')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => collect(range(1, 6))->map(fn ($i) => [
        '@type' => 'Question',
        'name' => __('marketing.faq_' . $i . '_q'),
        'acceptedAnswer' => [
            '@type' => 'Answer',
            'text' => __('marketing.faq_' . $i . '_a'),
        ],
    ])->values()->all(),
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
</script>
@endpush

@section('content')
{{-- no separate hero: the title sits beside the questions --}}
<section class="wo-wrap wo-pagesplit" aria-labelledby="wo-faq-title">
    <div class="wo-pagesplit__intro">
        <span class="wo-label">{{ __('marketing.faq_label') }}</span>
        <h1 id="wo-faq-title">{{ __('marketing.faq_title') }}</h1>
        <p>{{ __('marketing.faq_lead') }}</p>
    </div>

    {{-- the panel's open/close animation is driven by the layout script (.wo-faq details) --}}
    <div class="wo-faq wo-pagesplit__main">
        @foreach (range(1, 6) as $i)
            <details class="wo-faq-item" @if ($i === 1) open @endif>
                <summary>
                    <span class="wo-faq-q">{{ __('marketing.faq_' . $i . '_q') }}</span>
                    <span class="wo-faq-icon" aria-hidden="true"></span>
                </summary>
                <div class="wo-faq-panel">
                    <div class="wo-faq-panel-inner">
                        <p>{{ __('marketing.faq_' . $i . '_a') }}</p>
                    </div>
                </div>
            </details>
        @endforeach
    </div>

    <aside class="wo-faq-more wo-pagesplit__side">
        <h2>{{ __('marketing.faq_more_title') }}</h2>
        <p>{{ __('marketing.faq_more_text') }}</p>
        <a class="wo-textlink" href="mailto:{{ __('marketing.contact_email_address') }}">{{ __('marketing.contact_email_address') }}</a>
        <a class="wo-textlink wo-textlink--quiet" href="tel:+{{ __('marketing.phone_wa') }}">{{ __('marketing.phone_display') }}</a>
    </aside>
</section>

@include('marketing.partials.close', ['above' => 'white'])
@endsection
