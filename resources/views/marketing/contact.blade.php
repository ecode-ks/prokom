@extends('marketing.layout')

@section('content')
@php
    $intents = ['demo' => 'contact_intent_demo', 'pricing' => 'contact_intent_pricing', 'question' => 'contact_intent_question'];
    // ?intent=pricing (e.g. from the pricing section) preselects the reason
    $intent = old('intent', array_key_exists((string) request('intent'), $intents) ? request('intent') : 'demo');
    $fields = [
        ['name', 'text', 'contact_name', 'name', true],
        ['company', 'text', 'contact_company', 'organization', true],
        ['email', 'email', 'contact_email', 'email', true],
        ['phone', 'tel', 'contact_phone', 'tel', false],
    ];
    $icons = [
        'mail' => '<svg viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2.5" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="M4 7l8 6 8-6" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg>',
        'phone' => '<svg viewBox="0 0 24 24"><path d="M6.6 3.5h2.6l1.4 4.2-2 1.4a12 12 0 0 0 6.3 6.3l1.4-2 4.2 1.4v2.6a2 2 0 0 1-2.2 2A16.5 16.5 0 0 1 4.6 5.7a2 2 0 0 1 2-2.2z" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg>',
        'chat' => '<svg viewBox="0 0 24 24"><path d="M4 11.5a7.5 7.5 0 1 1 3.6 6.4L4 19l1.1-3.4A7.5 7.5 0 0 1 4 11.5z" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg>',
    ];
    $channels = [
        ['mail', 'contact_email', __('marketing.contact_email_address'), 'mailto:' . __('marketing.contact_email_address'), false],
        ['phone', 'contact_phone', __('marketing.phone_display'), 'tel:+' . __('marketing.phone_wa'), false],
        ['chat', 'whatsapp', __('marketing.whatsapp_cta'), 'https://wa.me/' . __('marketing.phone_wa'), true],
    ];
@endphp

{{-- no separate hero: the title and the direct channels sit beside the form --}}
<section class="wo-wrap wo-pagesplit" aria-labelledby="wo-contact-title">
    <div class="wo-pagesplit__intro">
        <span class="wo-label">{{ __('marketing.contact_label') }}</span>
        <h1 id="wo-contact-title">{{ __('marketing.contact_title') }}</h1>
        <p>{{ __('marketing.contact_lead') }}</p>
    </div>

    <form class="wo-form wo-pagesplit__main" id="forma" method="POST" action="{{ route('marketing.contact.submit') }}">
        @csrf
        <h2>{{ __('marketing.contact_form_title') }}</h2>

        @if (session('contact_success'))
            <p class="wo-form__note wo-form__note--ok" role="status">{{ session('contact_success') }}</p>
        @elseif (session('contact_error'))
            <p class="wo-form__note wo-form__note--err" role="alert">{{ session('contact_error') }}</p>
        @endif

        <fieldset class="wo-form__intent">
            <legend>{{ __('marketing.contact_intent') }}</legend>
            <div class="wo-seg">
                @foreach ($intents as $value => $key)
                    <label>
                        <input type="radio" name="intent" value="{{ $value }}" @checked($intent === $value)>
                        <span>{{ __('marketing.' . $key) }}</span>
                    </label>
                @endforeach
            </div>
            @error('intent')<p class="wo-field__err">{{ $message }}</p>@enderror
        </fieldset>

        <div class="wo-form__grid">
            @foreach ($fields as [$name, $type, $label, $autocomplete, $required])
                <div class="wo-field @error($name) is-invalid @enderror">
                    <label for="f-{{ $name }}">
                        {{ __('marketing.' . $label) }}
                        @unless ($required)<span class="wo-field__opt">{{ __('marketing.contact_optional') }}</span>@endunless
                    </label>
                    <input id="f-{{ $name }}" name="{{ $name }}" type="{{ $type }}" value="{{ old($name) }}" autocomplete="{{ $autocomplete }}" maxlength="{{ $name === 'phone' ? 40 : 120 }}"
                        @if ($required) required @endif
                        @if ($name === 'name' || $name === 'company') minlength="2" @endif
                        @error($name) aria-invalid="true" aria-describedby="f-{{ $name }}-err" @enderror>
                    @error($name)<p class="wo-field__err" id="f-{{ $name }}-err">{{ $message }}</p>@enderror
                </div>
            @endforeach

            <div class="wo-field wo-field--wide @error('message') is-invalid @enderror">
                <label for="f-message">{{ __('marketing.contact_message') }}</label>
                <textarea id="f-message" name="message" rows="5" required minlength="10" maxlength="2000" placeholder="{{ __('marketing.contact_message_hint') }}"
                    @error('message') aria-invalid="true" aria-describedby="f-message-err" @enderror>{{ old('message') }}</textarea>
                @error('message')<p class="wo-field__err" id="f-message-err">{{ $message }}</p>@enderror
            </div>
        </div>

        {{-- left empty by people; bots that fill it are dropped by the controller --}}
        <div class="wo-form__trap" aria-hidden="true">
            <label for="f-website">Website</label>
            <input id="f-website" name="website" type="text" tabindex="-1" autocomplete="off">
        </div>

        <div class="wo-form__foot">
            <button class="wo-btn wo-btn-primary" type="submit">{{ __('marketing.contact_submit') }}</button>
            <p>{{ __('marketing.contact_privacy_note') }} <a href="{{ route('marketing.privacy') }}">{{ __('marketing.footer_privacy') }}</a></p>
        </div>
    </form>

    <aside class="wo-channels wo-pagesplit__side" aria-labelledby="wo-channels-title">
        <h2 id="wo-channels-title">{{ __('marketing.contact_channels') }}</h2>
        <p>{{ __('marketing.contact_follow') }}</p>
        <ul class="wo-channels__list">
            @foreach ($channels as [$icon, $label, $value, $href, $external])
                <li>
                    <a class="wo-channel" href="{{ $href }}" @if ($external) target="_blank" rel="noopener noreferrer" @endif>
                        <span class="wo-channel__icon" aria-hidden="true">{!! $icons[$icon] !!}</span>
                        <span class="wo-channel__text">
                            <small>{{ __('marketing.' . $label) }}</small>
                            <strong>{{ $value }}</strong>
                        </span>
                        <span class="wo-channel__go" aria-hidden="true">↗</span>
                    </a>
                </li>
            @endforeach
        </ul>
        <div class="wo-channels__social">
            <span>{{ __('marketing.contact_social') }}</span>
            @include('marketing.partials.social')
        </div>
    </aside>
</section>
@endsection
