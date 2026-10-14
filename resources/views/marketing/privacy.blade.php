@extends('marketing.layout')

@section('content')
@php
    $sections = __('privacy.sections');
    $email = __('marketing.contact_email_address');
    $phone = __('marketing.phone_display');
    // Escape the text, then turn our email address and phone number into links.
    $linkify = fn (string $text) => str_replace(
        [e($email), e($phone)],
        ['<a href="mailto:' . e($email) . '">' . e($email) . '</a>', '<a href="tel:+' . e(__('marketing.phone_wa')) . '">' . e($phone) . '</a>'],
        e($text)
    );
@endphp

<article class="wo-legal" aria-labelledby="wo-legal-title">
    <header class="wo-legal__head">
        <div class="wo-wrap">
            <span class="wo-label">{{ __('privacy.label') }}</span>
            <h1 id="wo-legal-title">{{ __('privacy.title') }}</h1>
            <p class="wo-legal__updated">{{ __('privacy.updated') }}</p>
            <p class="wo-legal__intro">{{ __('privacy.intro') }}</p>
        </div>
    </header>

    <div class="wo-wrap wo-legal__body">
        <nav class="wo-legal__toc" aria-labelledby="wo-legal-toc">
            <p id="wo-legal-toc">{{ __('privacy.toc') }}</p>
            <ol>
                @foreach ($sections as $section)
                    <li><a href="#{{ $section['id'] }}">{{ $section['title'] }}</a></li>
                @endforeach
            </ol>
        </nav>

        <div class="wo-legal__content">
            @foreach ($sections as $i => $section)
                <section id="{{ $section['id'] }}" aria-labelledby="{{ $section['id'] }}-title">
                    <h2 id="{{ $section['id'] }}-title"><span>{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>{{ $section['title'] }}</h2>
                    @foreach ($section['content'] as $block)
                        @if (isset($block['h']))
                            <h3>{{ $block['h'] }}</h3>
                        @elseif (isset($block['ul']))
                            <ul>
                                @foreach ($block['ul'] as $item)
                                    <li>{!! $linkify($item) !!}</li>
                                @endforeach
                            </ul>
                        @else
                            <p>{!! $linkify($block['p']) !!}</p>
                        @endif
                    @endforeach
                </section>
            @endforeach

            <aside class="wo-legal__contact">
                <p>{{ __('privacy.contact_cta') }}</p>
                <a class="wo-btn wo-btn-primary" href="mailto:{{ $email }}">{{ $email }}</a>
            </aside>
        </div>
    </div>
</article>
@endsection
