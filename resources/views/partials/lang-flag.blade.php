@if(($code ?? '') === 'sq')
    <span class="lang-flag" aria-hidden="true">
        <img src="{{ asset('assets/flags/al.png') }}" alt="" width="21" height="15" style="width:100%;height:100%;max-width:1.35rem;max-height:0.92rem;object-fit:cover;display:block">
    </span>
@elseif(($code ?? '') === 'en')
    <span class="lang-flag" aria-hidden="true">
        <svg viewBox="0 0 21 15" xmlns="http://www.w3.org/2000/svg">
            <rect width="21" height="15" fill="#012169"/>
            <path d="M0,0 L21,15 M21,0 L0,15" stroke="#fff" stroke-width="3"/>
            <path d="M0,0 L21,15 M21,0 L0,15" stroke="#c8102e" stroke-width="2"/>
            <path d="M10.5,0 V15 M0,7.5 H21" stroke="#fff" stroke-width="5"/>
            <path d="M10.5,0 V15 M0,7.5 H21" stroke="#c8102e" stroke-width="3"/>
        </svg>
    </span>
@else
    <span class="lang-flag" aria-hidden="true">
        <svg viewBox="0 0 21 15" xmlns="http://www.w3.org/2000/svg">
            <rect width="21" height="5" y="0" fill="#000"/>
            <rect width="21" height="5" y="5" fill="#dd0000"/>
            <rect width="21" height="5" y="10" fill="#ffce00"/>
        </svg>
    </span>
@endif
