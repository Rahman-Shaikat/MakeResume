@php
    $customTitle = $item->data['title'] ?? $item->data['name'] ?? '';
    $customTitleClasses = collect([
        'resume-custom-item-title',
        ($item->data['title_bold'] ?? false) ? 'is-bold' : null,
        ($item->data['title_italic'] ?? false) ? 'is-italic' : null,
        ($item->data['title_use_accent'] ?? false) ? 'is-template-colored' : null,
    ])->filter()->implode(' ');
@endphp

@if (filled($customTitle))
    @if (filled($item->data['url'] ?? null))
        <a class="{{ $customTitleClasses }}" href="{{ $item->data['url'] }}" target="_blank" rel="noopener">{{ $customTitle }}</a>
    @else
        <span class="{{ $customTitleClasses }}">{{ $customTitle }}</span>
    @endif
@endif
