{{--
    Product page illustration (decorative; the copy beside it carries the meaning):
    one shared data core feeding the three modules. Uses the "how it works" scene styles.
--}}
@php
    $icons = [
        'erp' => '<svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3.5" y="7" width="17" height="12.5" rx="2.5" fill="none" stroke="currentColor" stroke-width="1.8"/><path d="M9 7V5.5A1.5 1.5 0 0 1 10.5 4h3A1.5 1.5 0 0 1 15 5.5V7M3.5 12.5h17" fill="none" stroke="currentColor" stroke-width="1.8"/></svg>',
        'crm' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 19.5h16M7 16v-4M12 16V8M17 16V5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>',
        'hr' => '<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="9" cy="8.5" r="3.2" fill="none" stroke="currentColor" stroke-width="1.8"/><path d="M3.5 19c.6-3 2.8-4.6 5.5-4.6s4.9 1.6 5.5 4.6M15.5 5.6a3 3 0 0 1 0 5.8M17.2 14.6c1.8.5 3 1.9 3.3 4.4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>',
    ];
    // y of each card's centre in the 160 × 110 drawing; the links fan out from the core to these points
    $cards = ['erp' => 22, 'crm' => 55, 'hr' => 88];
@endphp
<div class="wo-scene wo-scene--map" aria-hidden="true">
    <div class="sc">
        <svg class="scm-links" viewBox="0 0 160 110">
            @foreach ($cards as $mod => $y)
                <path class="scm-link" d="M55 52 C72 52 70 {{ $y }} 88 {{ $y }}" />
                <path class="scm-pulse" d="M55 52 C72 52 70 {{ $y }} 88 {{ $y }}" pathLength="100" style="--d: {{ $loop->index * 1.1 }}s" />
            @endforeach
        </svg>

        <div class="scm-core">
            <span class="scm-core__ring"></span>
            <span class="scm-core__mark"></span>
        </div>
        <span class="sc-chip scm-core__label">{{ __('marketing.product_map_core') }}</span>

        @foreach ($cards as $mod => $y)
            <div class="sc-card scm-card" style="top: {{ ($y - 14) / 110 * 100 }}%">
                <div class="scm-card__head">
                    <span class="scm-card__icon">{!! $icons[$mod] !!}</span>
                    <span class="sc-h">{{ __('marketing.nav_' . $mod) }}</span>
                </div>
                <div class="scm-card__chips">
                    <span class="sc-chip">{{ __('marketing.product_map_' . $mod . '_1') }}</span>
                    <span class="sc-chip">{{ __('marketing.product_map_' . $mod . '_2') }}</span>
                </div>
            </div>
        @endforeach
    </div>
</div>
