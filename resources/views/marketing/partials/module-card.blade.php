{{--
    One module as a card, linking to its page (home, product page, "other modules" on a module page).
    The title is the link; it covers the whole card.
    $mod: erp | crm | hr
    $points: feature keys to list (optional)
--}}
@php
    $num = ['erp' => '01', 'crm' => '02', 'hr' => '03'][$mod];
    $points = $points ?? [];
@endphp
<article class="wo-card">
    <div class="wo-card__top">
        <span class="wo-card__icon" aria-hidden="true">@include('marketing.partials.module-icon', ['mod' => $mod])</span>
        <span class="wo-card__num" aria-hidden="true">{{ $num }}</span>
    </div>
    <h3 class="wo-card__title">
        <a href="{{ route('marketing.' . $mod) }}">{{ __('marketing.nav_' . $mod) }}</a>
    </h3>
    <p class="wo-card__text">{{ __('marketing.' . $mod . '_text') }}</p>
    @if ($points)
        <ul class="wo-card__list">
            @foreach ($points as $point)
                <li>{{ __('marketing.' . $point) }}</li>
            @endforeach
        </ul>
    @endif
    <span class="wo-card__more" aria-hidden="true">
        {{ __('marketing.module_open') }}
        <svg viewBox="0 0 16 16"><path d="M3 8h9.5M8.5 4l4 4-4 4" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
    </span>
</article>
