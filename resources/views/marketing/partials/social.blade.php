{{-- Social profiles as icon buttons (footer, contact page). $class: extra class for the list. --}}
<ul class="wo-social {{ $class ?? '' }}" aria-label="{{ __('marketing.footer_social') }}">
    <li>
        <a href="{{ __('marketing.social_linkedin') }}" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M4.98 3.5a2.48 2.48 0 1 1 0 4.96 2.48 2.48 0 0 1 0-4.96zM3 9h4v12H3zM9.5 9h3.8v1.7h.05c.53-1 1.83-2.05 3.77-2.05 4.03 0 4.78 2.65 4.78 6.1V21h-4v-5.5c0-1.31-.02-3-1.83-3-1.83 0-2.11 1.43-2.11 2.9V21h-4z"/></svg>
        </a>
    </li>
    <li>
        <a href="{{ __('marketing.social_instagram') }}" target="_blank" rel="noopener noreferrer" aria-label="Instagram">
            <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="5" fill="none" stroke="currentColor" stroke-width="1.8"/><circle cx="12" cy="12" r="4.2" fill="none" stroke="currentColor" stroke-width="1.8"/><circle cx="17.4" cy="6.6" r="1.1" fill="currentColor"/></svg>
        </a>
    </li>
    <li>
        <a href="{{ __('marketing.social_tiktok') }}" target="_blank" rel="noopener noreferrer" aria-label="TikTok">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M16.6 3c.3 2.3 1.8 3.9 4.1 4.1v3.1c-1.5.1-2.9-.4-4.1-1.2v6.4c0 3.3-2.6 5.6-5.7 5.6-3.2 0-5.6-2.5-5.6-5.6 0-3.4 2.9-6 6.4-5.5v3.3c-1.6-.5-3.2.6-3.2 2.2 0 1.3 1 2.3 2.4 2.3 1.4 0 2.4-1 2.4-2.6V3z"/></svg>
        </a>
    </li>
</ul>
