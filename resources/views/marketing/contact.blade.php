@extends('marketing.layout')

@section('content')
<div class="wo-wrap">
    <section class="wo-section" style="padding-top:3rem">
        <div class="wo-contact-grid">
            <div>
                <span class="wo-label">{{ __('marketing.contact_label') }}</span>
                <h2>{{ __('marketing.contact_title') }}</h2>
                <p class="wo-lead">{{ __('marketing.contact_lead') }}</p>
                <div class="wo-contact-direct">
                    <a class="wo-btn wo-btn-primary" href="https://wa.me/{{ __('marketing.phone_wa') }}" target="_blank" rel="noopener noreferrer">{{ __('marketing.whatsapp_cta') }}</a>
                </div>
            </div>

            <form class="wo-form" method="post" action="{{ route('marketing.contact.submit') }}">
                @csrf
                @if(session('contact_success'))
                    <div class="wo-alert wo-alert-ok">{{ session('contact_success') }}</div>
                @endif
                @if(session('contact_error'))
                    <div class="wo-alert wo-alert-err">{{ session('contact_error') }}</div>
                @endif
                <div class="row">
                    <label>
                        {{ __('marketing.contact_name') }}
                        <input type="text" name="name" value="{{ old('name') }}" required autocomplete="name">
                    </label>
                    <label>
                        {{ __('marketing.contact_company') }}
                        <input type="text" name="company" value="{{ old('company') }}" required autocomplete="organization">
                    </label>
                </div>
                <div class="row">
                    <label>
                        {{ __('marketing.contact_email') }}
                        <input type="email" name="email" value="{{ old('email') }}" required autocomplete="email">
                    </label>
                    <label>
                        {{ __('marketing.contact_phone') }}
                        <input type="text" name="phone" value="{{ old('phone') }}" autocomplete="tel">
                    </label>
                </div>
                <label>
                    {{ __('marketing.contact_intent') }}
                    <select name="intent" required>
                        <option value="demo" @selected(old('intent', 'demo') === 'demo')>{{ __('marketing.contact_intent_demo') }}</option>
                        <option value="pricing" @selected(old('intent') === 'pricing')>{{ __('marketing.contact_intent_pricing') }}</option>
                        <option value="question" @selected(old('intent') === 'question')>{{ __('marketing.contact_intent_question') }}</option>
                    </select>
                </label>
                <label>
                    {{ __('marketing.contact_message') }}
                    <textarea name="message" required>{{ old('message') }}</textarea>
                </label>
                <button type="submit" class="wo-btn wo-btn-primary">{{ __('marketing.contact_submit') }}</button>
            </form>
        </div>
    </section>
</div>
@endsection
