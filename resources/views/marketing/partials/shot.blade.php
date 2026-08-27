@php
    $path = public_path('images/marketing/' . $file);
    $hasImage = is_file($path);
    $src = $hasImage ? asset('images/marketing/' . $file) : null;
    $caption = $caption ?? null;
    $placeholder = "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='10'%3E%3Crect width='16' height='10' fill='%23e8eef6'/%3E%3C/svg%3E";
@endphp
<figure class="wo-shot {{ $class ?? '' }}">
    <div class="wo-shot-body">
        @if($hasImage)
            <button
                type="button"
                class="wo-shot-open"
                data-wo-lightbox="{{ $src }}"
                data-wo-lightbox-label="{{ $label }}"
                aria-label="{{ $label }}"
            >
                <img
                    class="wo-lazy"
                    src="{{ $placeholder }}"
                    data-wo-src="{{ $src }}"
                    alt="{{ $label }} — WORKOM"
                    loading="lazy"
                    decoding="async"
                >
            </button>
        @else
            <div class="wo-shot-empty">
                <strong>{{ __('marketing.shot_placeholder') }}</strong>
                <div>{{ $label }}</div>
                <small>{{ __('marketing.shot_hint') }}{{ $file }}</small>
            </div>
        @endif
    </div>
    @if((($label ?? null) || ($caption ?? null)) && empty($hideCaption))
        <figcaption class="wo-shot-caption">
            @if($label)
                <strong>{{ $label }}</strong>
            @endif
            @if($caption)
                <p>{{ $caption }}</p>
            @endif
        </figcaption>
    @endif
</figure>
