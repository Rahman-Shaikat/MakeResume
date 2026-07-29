@php
    $headingType = $type ?? 'custom';
@endphp

<div class="template-three-section-heading">
    <span class="template-three-heading-icon" aria-hidden="true">
        @switch($headingType)
            @case('contact')
                <svg viewBox="0 0 24 24"><path d="M3 6.5 12 13l9-6.5M4 5h16a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Z"/></svg>
                @break
            @case('summary')
                <svg viewBox="0 0 24 24"><circle cx="12" cy="7" r="4"/><path d="M4 21v-3a6 6 0 0 1 6-6h4a6 6 0 0 1 6 6v3Z"/></svg>
                @break
            @case('experience')
                <svg viewBox="0 0 24 24"><circle cx="12" cy="6" r="3"/><path d="M7 21v-5l-2 4-3-2 4-7h12l4 7-3 2-2-4v5M10 12l2 4 2-4"/></svg>
                @break
            @case('education')
                <svg viewBox="0 0 24 24"><path d="m2 9 10-4 10 4-10 4L2 9Zm4 2.5V17c3.4 2.4 8.6 2.4 12 0v-5.5M21 10v7"/></svg>
                @break
            @case('skills')
                <svg viewBox="0 0 24 24"><path d="m4 14 5-5 3 3 6-6 2 2-6 6 3 3-5 5-3-3-3 1-2-2 1-3-1-1Z"/></svg>
                @break
            @case('awards')
                <svg viewBox="0 0 24 24"><path d="M5 3v18M6 5h11l-2.5 4L17 13H6"/></svg>
                @break
            @case('languages')
                <svg viewBox="0 0 24 24"><path d="M4 5h10M9 3v2c0 5-2 8-6 10M6 10c2 2 4 3 7 4M15 8l5 13M13 17h8"/></svg>
                @break
            @case('courses')
                <svg viewBox="0 0 24 24"><path d="M5 3h12a2 2 0 0 1 2 2v16H7a2 2 0 0 1-2-2V3Zm2 14h12M9 7h6M9 11h6"/></svg>
                @break
            @case('projects')
                <svg viewBox="0 0 24 24"><path d="M3 7h7l2 2h9v11H3V7Zm0 0V5h7l2 2"/></svg>
                @break
            @default
                <svg viewBox="0 0 24 24"><path d="M12 21 4.5 13.5a5 5 0 0 1 7.1-7.1l.4.4.4-.4a5 5 0 0 1 7.1 7.1L12 21Z"/></svg>
        @endswitch
    </span>
    <h3>{{ $title }}</h3>
</div>
