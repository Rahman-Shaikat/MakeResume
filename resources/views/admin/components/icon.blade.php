<svg class="admin-icon" viewBox="0 0 24 24" aria-hidden="true">
    @switch($name)
        @case('activity')
            <path d="M3 12h4l2.5-7 5 14 2.5-7h4"/>
            @break
        @case('alert')
            <path d="M12 3 2.8 20h18.4L12 3Z"/><path d="M12 9v4m0 3h.01"/>
            @break
        @case('bell')
            <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4"/>
            @break
        @case('chart')
            <path d="M4 20V10m6 10V4m6 16v-7m4 7H2"/>
            @break
        @case('check')
            <path d="m5 12 4 4L19 6"/>
            @break
        @case('chevron-down')
            <path d="m7 10 5 5 5-5"/>
            @break
        @case('dashboard')
            <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>
            @break
        @case('document')
            <path d="M6 2h8l4 4v16H6z"/><path d="M14 2v5h5M9 12h6m-6 4h6"/>
            @break
        @case('external')
            <path d="M14 4h6v6m0-6-9 9"/><path d="M18 13v7H4V6h7"/>
            @break
        @case('help')
            <circle cx="12" cy="12" r="9"/><path d="M9.6 9a2.5 2.5 0 1 1 3.3 2.4c-.9.4-.9 1.1-.9 1.6m0 3h.01"/>
            @break
        @case('layout')
            <rect x="3" y="4" width="18" height="16" rx="2"/><path d="M9 4v16M9 10h12"/>
            @break
        @case('logout')
            <path d="M10 5H5v14h5m4-11 4 4-4 4m4-4H9"/>
            @break
        @case('menu')
            <path d="M4 7h16M4 12h16M4 17h16"/>
            @break
        @case('more')
            <circle cx="5" cy="12" r="1"/><circle cx="12" cy="12" r="1"/><circle cx="19" cy="12" r="1"/>
            @break
        @case('resume')
            <path d="M7 3h8l4 4v14H7zM15 3v5h4"/><circle cx="11" cy="11" r="2"/><path d="M8.5 17a2.5 2.5 0 0 1 5 0"/>
            @break
        @case('refresh')
            <path d="M20 7v5h-5"/><path d="M4 17v-5h5"/><path d="M6.1 8a7 7 0 0 1 11.8-1L20 9M4 15l2.1 2a7 7 0 0 0 11.8-1"/>
            @break
        @case('search')
            <circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/>
            @break
        @case('settings')
            <circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.9l.1.1-2.8 2.8-.1-.1a1.7 1.7 0 0 0-1.9-.3 1.7 1.7 0 0 0-1 1.6v.2h-4V21a1.7 1.7 0 0 0-1-1.6 1.7 1.7 0 0 0-1.9.3l-.1.1L4.2 17l.1-.1a1.7 1.7 0 0 0 .3-1.9A1.7 1.7 0 0 0 3 14H2.8v-4H3a1.7 1.7 0 0 0 1.6-1 1.7 1.7 0 0 0-.3-1.9L4.2 7 7 4.2l.1.1A1.7 1.7 0 0 0 9 4.6a1.7 1.7 0 0 0 1-1.6v-.2h4V3a1.7 1.7 0 0 0 1 1.6 1.7 1.7 0 0 0 1.9-.3l.1-.1L19.8 7l-.1.1a1.7 1.7 0 0 0-.3 1.9 1.7 1.7 0 0 0 1.6 1h.2v4H21a1.7 1.7 0 0 0-1.6 1Z"/>
            @break
        @case('shield')
            <path d="M12 3 5 6v5c0 5 3 8 7 10 4-2 7-5 7-10V6l-7-3Z"/><path d="m9 12 2 2 4-4"/>
            @break
        @case('sidebar')
            <rect x="3" y="4" width="18" height="16" rx="2"/><path d="M9 4v16m5-11-3 3 3 3"/>
            @break
        @case('sparkles')
            <path d="m12 3 1.1 3.2L16 7.5l-2.9 1.3L12 12l-1.1-3.2L8 7.5l2.9-1.3L12 3Zm6 9 .8 2.2L21 15l-2.2.8L18 18l-.8-2.2L15 15l2.2-.8L18 12ZM6 13l1 2.7 2.5 1L7 17.8 6 21l-1-3.2-2.5-1 2.5-1L6 13Z"/>
            @break
        @case('trend')
            <path d="m4 16 5-5 4 4 7-8"/><path d="M15 7h5v5"/>
            @break
        @case('user')
            <circle cx="12" cy="8" r="4"/><path d="M5 21a7 7 0 0 1 14 0"/>
            @break
        @case('user-plus')
            <circle cx="9" cy="8" r="4"/><path d="M2 21a7 7 0 0 1 14 0m3-12v6m-3-3h6"/>
            @break
        @case('users')
            <circle cx="9" cy="8" r="4"/><path d="M2 21a7 7 0 0 1 14 0m1-16a4 4 0 0 1 0 7m1 3a6 6 0 0 1 4 6"/>
            @break
        @default
            <circle cx="12" cy="12" r="9"/>
    @endswitch
</svg>
