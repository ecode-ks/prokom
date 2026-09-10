@php $locale = app()->getLocale(); @endphp
<div class="dropdown lang-dropdown d-inline-block">
    <button class="btn bg-custom rounded-3 border dropdown-toggle d-inline-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false" aria-label="{{ __('app.language') }}">
        <span class="lang-flag-wrap">@include('partials.lang-flag', ['code' => $locale])</span>
        {{ strtoupper($locale) }}
    </button>
    <ul class="dropdown-menu dropdown-menu-end lang-menu rounded-4 p-1">
        @foreach (['sq', 'en', 'de'] as $code)
            <li>
                <a class="dropdown-item lang-option {{ $locale === $code ? 'is-active' : '' }}" href="{{ route('marketing.locale', $code) }}">
                    <span class="lang-flag-wrap">@include('partials.lang-flag', ['code' => $code])</span>
                    <span class="lang-option-name">{{ __('app.language_' . $code) }}</span>
                    <i class="fa-solid fa-check lang-option-check" aria-hidden="true"></i>
                </a>
            </li>
        @endforeach
    </ul>
</div>
