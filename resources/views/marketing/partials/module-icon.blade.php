{{-- Line icon for a module (decorative). $mod: erp | crm | hr --}}
@switch($mod)
    @case('erp')
        <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3.5" y="7" width="17" height="12.5" rx="2.5" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="M9 7V5.5A1.5 1.5 0 0 1 10.5 4h3A1.5 1.5 0 0 1 15 5.5V7M3.5 12.5h17" fill="none" stroke="currentColor" stroke-width="1.6"/></svg>
        @break
    @case('crm')
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 19.5h16M7 16v-4M12 16V8M17 16V5" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
        @break
    @case('hr')
        <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="9" cy="8.5" r="3.2" fill="none" stroke="currentColor" stroke-width="1.6"/><path d="M3.5 19c.6-3 2.8-4.6 5.5-4.6s4.9 1.6 5.5 4.6M15.5 5.6a3 3 0 0 1 0 5.8M17.2 14.6c1.8.5 3 1.9 3.3 4.4" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
        @break
@endswitch
