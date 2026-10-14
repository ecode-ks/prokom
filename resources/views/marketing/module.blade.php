@extends('marketing.layout')

@section('content')
@php
    $features = collect(range(1, 5))->map(fn ($n) => [
        't' => __('marketing.' . $module . '_f' . $n . '_t'),
        'd' => __('marketing.' . $module . '_f' . $n . '_d'),
    ]);
    $shots = collect($shots ?? [])
        ->filter(fn ($shot) => is_file(public_path('images/marketing/' . $shot['file'])))
        ->map(function ($shot) {
            [$shot['w'], $shot['h']] = getimagesize(public_path('images/marketing/' . $shot['file']));
            return $shot;
        })
        ->values();
    $others = array_values(array_diff(['erp', 'crm', 'hr'], [$module]));
@endphp

{{-- with screens, the head leaves room for the screens panel to rise over its lower edge --}}
<header class="wo-phead{{ $shots->isNotEmpty() ? ' wo-phead--lift' : '' }}">
    <div class="wo-wrap">
        <div class="wo-phead__grid wo-phead__grid--module">
            <div class="wo-phead__copy">
                <h1>{{ __('marketing.' . $module . '_title') }}</h1>
                <p class="wo-phead__lead">{{ __('marketing.' . $module . '_page_lead') }}</p>
            </div>
            <div class="wo-phead__side">
                <p class="wo-phead__explain">{{ __('marketing.' . $module . '_explain') }}</p>
                {{-- the demo request lives in the header and the closing section --}}
                <div class="wo-phead__links">
                    @if ($shots->isNotEmpty())
                        <a class="wo-textlink" href="#pamjet">{{ __('marketing.module_see_shots') }} <span aria-hidden="true">↓</span></a>
                    @endif
                    <a class="wo-textlink wo-textlink--quiet" href="{{ route('home') }}#cmimet">{{ __('marketing.product_cta_pricing') }} <span aria-hidden="true">→</span></a>
                </div>
            </div>
        </div>
    </div>
</header>

{{-- Real screens: a list of views beside the open screen. Without JS every screen simply shows, one under the other. --}}
@if ($shots->isNotEmpty())
    <section class="wo-wrap wo-shots" id="pamjet" aria-labelledby="wo-shots-title" data-shots>
        <h2 class="wo-sr" id="wo-shots-title">{{ __('marketing.module_shots_title') }}</h2>
        <div class="wo-shots__tabs" role="tablist" aria-labelledby="wo-shots-title">
            @foreach ($shots as $i => $shot)
                <button type="button" class="wo-shots__tab" role="tab" id="wo-shot-tab-{{ $i }}" aria-controls="wo-shot-{{ $i }}" aria-selected="{{ $i === 0 ? 'true' : 'false' }}" tabindex="{{ $i === 0 ? '0' : '-1' }}">
                    <span class="wo-shots__label">{{ $shot['label'] }}</span>
                    <span class="wo-shots__caption">{{ $shot['caption'] }}</span>
                </button>
            @endforeach
        </div>

        <div class="wo-shots__frame">
            @foreach ($shots as $i => $shot)
                <figure class="wo-shots__panel" role="tabpanel" id="wo-shot-{{ $i }}" aria-labelledby="wo-shot-tab-{{ $i }}">
                    <button type="button" class="wo-shots__zoom" data-wo-lightbox="{{ asset('images/marketing/' . $shot['file']) }}" data-wo-lightbox-label="{{ $shot['label'] }}">
                        <img src="{{ asset('images/marketing/' . $shot['file']) }}" alt="{{ $shot['label'] }} — WORKOM" width="{{ $shot['w'] }}" height="{{ $shot['h'] }}" @if ($i > 0) loading="lazy" @endif decoding="async">
                    </button>
                    <figcaption><strong>{{ $shot['label'] }}</strong> {{ $shot['caption'] }}</figcaption>
                </figure>
            @endforeach
            <p class="wo-shots__hint">{{ __('marketing.module_shots_lead') }}</p>
        </div>
    </section>
@endif

<section class="wo-wrap wo-section" id="funksionet" aria-labelledby="wo-feats-title">
    <div class="wo-splithead">
        <div>
            <span class="wo-label">{{ __('marketing.module_includes') }}</span>
            <h2 id="wo-feats-title">{{ __('marketing.module_includes_title') }}</h2>
        </div>
        <p class="wo-lead">{{ __('marketing.' . $module . '_includes_lead') }}</p>
    </div>
    <ol class="wo-cards">
        @foreach ($features as $i => $feature)
            <li class="wo-card">
                <span class="wo-card__icon wo-card__icon--num" aria-hidden="true">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                <h3 class="wo-card__title">{{ $feature['t'] }}</h3>
                <p class="wo-card__text">{{ $feature['d'] }}</p>
            </li>
        @endforeach
    </ol>
</section>

<section class="wo-wrap wo-section wo-more" aria-labelledby="wo-more-title">
    <div class="wo-splithead">
        <div>
            <span class="wo-label">{{ __('marketing.module_also') }}</span>
            <h2 id="wo-more-title">{{ __('marketing.module_also_title') }}</h2>
        </div>
    </div>
    <div class="wo-cards wo-cards--two wo-cards--swipe">
        @foreach ($others as $mod)
            @include('marketing.partials.module-card', ['mod' => $mod])
        @endforeach
    </div>
</section>

@include('marketing.partials.close', ['above' => 'white'])
@endsection

@push('scripts')
<script>
    // Screens: tabs switch the open screen (arrow keys move between tabs).
    (function () {
        var box = document.querySelector('[data-shots]');
        if (!box) return;
        var list = box.querySelector('[role="tablist"]');
        var tabs = Array.prototype.slice.call(box.querySelectorAll('[role="tab"]'));
        var panels = tabs.map(function (tab) { return document.getElementById(tab.getAttribute('aria-controls')); });

        function select(i, focus) {
            tabs.forEach(function (tab, n) {
                var on = n === i;
                tab.setAttribute('aria-selected', on ? 'true' : 'false');
                tab.tabIndex = on ? 0 : -1;
                panels[n].hidden = !on;
            });
            if (focus) tabs[i].focus({ preventScroll: true });
            // phones: keep the active tab in view inside the scrolling row, without moving the page
            if (list.scrollWidth > list.clientWidth) {
                var tab = tabs[i];
                list.scrollTo({ left: tab.offsetLeft - (list.clientWidth - tab.offsetWidth) / 2, behavior: 'smooth' });
            }
        }

        tabs.forEach(function (tab, i) {
            tab.addEventListener('click', function () { select(i, false); });
            tab.addEventListener('keydown', function (e) {
                var next = { ArrowRight: i + 1, ArrowDown: i + 1, ArrowLeft: i - 1, ArrowUp: i - 1, Home: 0, End: tabs.length - 1 }[e.key];
                if (next === undefined) return;
                e.preventDefault();
                select((next + tabs.length) % tabs.length, true);
            });
        });

        box.classList.add('is-ready');
        select(0, false);
    })();
</script>
@endpush
