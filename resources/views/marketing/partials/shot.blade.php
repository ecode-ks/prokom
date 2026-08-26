@php
    $path = public_path('images/marketing/' . $file);
    $hasImage = is_file($path);
@endphp
<figure class="wo-shot {{ $class ?? '' }}">
    <div class="wo-shot-bar" aria-hidden="true">
        <i></i><i></i><i></i>
        <span>{{ $label }}</span>
    </div>
    <div class="wo-shot-body">
        @if($hasImage)
            <img src="{{ asset('images/marketing/' . $file) }}" alt="{{ $label }} — WorkOM" loading="lazy">
        @else
            <div class="wo-shot-empty">
                <strong>{{ __('marketing.shot_placeholder') }}</strong>
                <div>{{ $label }}</div>
                <small>{{ __('marketing.shot_hint') }}{{ $file }}</small>
            </div>
        @endif
    </div>
</figure>
