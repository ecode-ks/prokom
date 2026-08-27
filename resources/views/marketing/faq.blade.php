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
<div class="wo-wrap container" style="max-width:760px">
    <section class="wo-section" style="padding-top:3rem">
        <span class="wo-label">{{ __('marketing.faq_label') }}</span>
        <h2>{{ __('marketing.faq_title') }}</h2>
        <div class="wo-faq">
            @for ($i = 1; $i <= 6; $i++)
                <details class="wo-faq-item" @if($i === 1) open @endif>
                    <summary>{{ __('marketing.faq_' . $i . '_q') }}</summary>
                    <div class="wo-faq-panel">
                        <div class="wo-faq-panel-inner">
                            <p>{{ __('marketing.faq_' . $i . '_a') }}</p>
                        </div>
                    </div>
                </details>
            @endfor
        </div>
        <div class="wo-cta-row" style="margin-top:1.75rem">
            <a class="wo-btn wo-btn-primary" href="{{ route('marketing.contact') }}">{{ __('marketing.nav_demo') }}</a>
        </div>
    </section>
</div>
@endsection
