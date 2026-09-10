@php
    $selected = $selected ?? 'sq';
    $mode = $mode ?? 'radio';
@endphp
<div class="dropdown lang-dropdown">
    @if ($mode === 'submit')
        <button class="btn bg-custom rounded-3 border dropdown-toggle d-inline-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            <span class="lang-flag-wrap">@include('partials.lang-flag', ['code' => $selected])</span>
            {{ __('app.language_' . $selected) }}
        </button>
        <ul class="dropdown-menu lang-menu rounded-4 p-1">
            @foreach (['sq', 'en', 'de'] as $code)
                <li>
                    <button type="submit" name="language" value="{{ $code }}" class="dropdown-item lang-option {{ $selected === $code ? 'is-active' : '' }}">
                        <span class="lang-flag-wrap">@include('partials.lang-flag', ['code' => $code])</span>
                        <span class="lang-option-name">{{ __('app.language_' . $code) }}</span>
                        <i class="fa-solid fa-check lang-option-check" aria-hidden="true"></i>
                    </button>
                </li>
            @endforeach
        </ul>
    @else
        <button class="btn bg-custom rounded-3 border dropdown-toggle w-100 d-flex align-items-center" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="languageToggle">
            <span class="lang-toggle-flag lang-flag-wrap">@include('partials.lang-flag', ['code' => $selected])</span>
            <span id="languageToggleLabel">{{ __('app.language_' . $selected) }}</span>
        </button>
        <ul class="dropdown-menu lang-menu rounded-4 p-1 w-100">
            @foreach (['sq', 'en', 'de'] as $code)
                <li>
                    <label class="dropdown-item lang-option mb-0 {{ $selected === $code ? 'is-active' : '' }}">
                        <input type="radio" class="visually-hidden lang-radio" name="language" value="{{ $code }}" data-label="{{ __('app.language_' . $code) }}" @checked($selected === $code)>
                        <span class="lang-flag-wrap">@include('partials.lang-flag', ['code' => $code])</span>
                        <span class="lang-option-name">{{ __('app.language_' . $code) }}</span>
                        <i class="fa-solid fa-check lang-option-check" aria-hidden="true"></i>
                    </label>
                </li>
            @endforeach
        </ul>
    @endif
</div>
@if ($mode !== 'submit')
<script>
    document.querySelectorAll('.lang-dropdown .lang-radio').forEach(function (input) {
        input.addEventListener('change', function () {
            var item = this.closest('.lang-option');
            var menu = this.closest('.dropdown-menu');
            var toggle = document.getElementById('languageToggle');
            var toggleLabel = document.getElementById('languageToggleLabel');
            var flagWrap = toggle && toggle.querySelector('.lang-toggle-flag');
            var flag = item.querySelector('.lang-flag');
            if (toggleLabel) toggleLabel.textContent = this.getAttribute('data-label');
            if (flag && flagWrap) flagWrap.innerHTML = flag.outerHTML;
            menu.querySelectorAll('.lang-option').forEach(function (el) {
                el.classList.remove('is-active');
            });
            item.classList.add('is-active');
        });
    });
</script>
@endif
