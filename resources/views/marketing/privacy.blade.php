@extends('marketing.layout')

{{-- Styles live in the page: marketing.css is served cache-first by the service worker,
     so returning visitors would not get new rules added there. --}}
@push('head')
<style>
    .wo-legal__head { padding: clamp(2.5rem, 2rem + 3vw, 4.5rem) 0 clamp(2rem, 1.5rem + 2vw, 3rem); border-bottom: 1px solid var(--line); }
    .wo-legal h1, .wo-legal h2 { overflow-wrap: break-word; hyphens: auto; }
    .wo-legal__head h1 { margin: 0; font-family: var(--display); font-size: clamp(2rem, 1.4rem + 3vw, 3.25rem); font-weight: 700; line-height: 1.1; letter-spacing: -0.035em; color: var(--navy); }
    .wo-legal__updated { margin: .75rem 0 0; font-size: .875rem; color: var(--muted); }
    .wo-legal__intro { max-width: 46rem; margin: 1.25rem 0 0; font-size: 1.125rem; line-height: 1.65; color: var(--muted); }
    .wo-legal__body { display: grid; gap: 2.5rem; padding: clamp(2.5rem, 2rem + 3vw, 4rem) 0 2rem; }
    .wo-legal__toc { padding: 1.25rem 1.5rem; background: var(--bg-2); border: 1px solid var(--line); border-radius: 14px; }
    .wo-legal__toc > p { margin: 0 0 .75rem; font-size: .75rem; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; color: var(--muted); }
    .wo-legal__toc ol { display: grid; gap: .375rem; margin: 0; padding-left: 1.25rem; font-size: .9375rem; color: var(--muted); }
    .wo-legal__toc a { color: var(--text); }
    .wo-legal__toc a:hover { color: var(--accent); }
    .wo-legal__content { max-width: 46rem; min-width: 0; }
    .wo-legal__content section { scroll-margin-top: 110px; }
    .wo-legal__content section + section { margin-top: 2.5rem; padding-top: 2.5rem; border-top: 1px solid var(--line); }
    .wo-legal__content h2 { display: flex; align-items: baseline; gap: .75rem; margin: 0 0 1rem; font-family: var(--display); font-size: clamp(1.25rem, 1.1rem + .6vw, 1.5rem); font-weight: 650; letter-spacing: -0.02em; color: var(--navy); }
    .wo-legal__content h2 span { font-size: .8125rem; color: var(--accent); }
    .wo-legal__content h3 { margin: 1.5rem 0 .5rem; font-size: 1rem; font-weight: 700; color: var(--navy); }
    .wo-legal__content p, .wo-legal__content li { line-height: 1.7; color: var(--muted); }
    .wo-legal__content p { margin: 0 0 1rem; }
    .wo-legal__content ul { display: grid; gap: .5rem; margin: 0 0 1rem; padding: 0; list-style: none; }
    .wo-legal__content li { position: relative; padding-left: 1.25rem; }
    .wo-legal__content li::before { content: ""; position: absolute; left: .25rem; top: .72em; width: .375rem; height: .375rem; border-radius: 50%; background: var(--accent); }
    .wo-legal__content a:not(.wo-btn) { color: var(--accent); text-decoration: underline; text-underline-offset: .15em; }
    .wo-legal__contact { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem; margin-top: 3rem; padding: 1.5rem; background: var(--bg-2); border: 1px solid var(--line); border-radius: 16px; }
    .wo-legal__contact p { margin: 0; font-family: var(--display); font-size: 1.0625rem; font-weight: 650; color: var(--navy); }
    @media (min-width: 960px) {
        .wo-legal__body { grid-template-columns: 240px minmax(0, 46rem); gap: 4.5rem; align-items: start; }
        .wo-legal__toc { position: sticky; top: 110px; padding: 0; background: none; border: 0; }
    }
</style>
@endpush

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
