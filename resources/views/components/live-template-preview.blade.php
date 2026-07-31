@props([
    'template',
    'previewUrl',
    'fallbackUrl' => null,
    'fallbackAlt' => null,
    'deferFallback' => false,
    'context' => 'dashboard',
    'priority' => 'normal',
])

@php
    $fallbackUrl ??= $template->thumbnailUrl();
    $fallbackAlt ??= "{$template->name} resume thumbnail";
@endphp

<div
    {{ $attributes->class('template-preview') }}
    style="--resume-accent: {{ $template->accent_color }}"
    data-live-template-preview
    data-live-preview-url="{{ $previewUrl }}"
    data-live-preview-title="{{ $template->name }} live preview"
    data-live-preview-context="{{ $context }}"
    data-live-preview-priority="{{ $priority }}"
    @if ($deferFallback && $fallbackUrl)
        data-live-preview-fallback-url="{{ $fallbackUrl }}"
        data-live-preview-fallback-alt="{{ $fallbackAlt }}"
    @endif
    data-preview-state="idle"
    aria-busy="true"
>
    <div class="template-preview-fallback" data-template-preview-fallback>
        @if ($fallbackUrl)
            @if ($deferFallback)
                <div class="template-preview-skeleton" aria-hidden="true"><span></span><span></span><span></span></div>
                <noscript>
                    <img src="{{ $fallbackUrl }}" alt="{{ $fallbackAlt }}" loading="lazy" width="700" height="990">
                </noscript>
            @else
                <img
                    src="{{ $fallbackUrl }}"
                    alt="{{ $fallbackAlt }}"
                    loading="lazy"
                    decoding="async"
                    width="700"
                    height="990"
                >
            @endif
        @else
            <div class="template-thumbnail-placeholder">
                <span>A4</span>
                <strong>{{ $template->name }}</strong>
            </div>
        @endif
        <span class="template-preview-loading" aria-hidden="true">
            <span class="spinner-border spinner-border-sm"></span>
            Loading live preview
        </span>
    </div>
    <div class="template-live-preview-mount" data-live-preview-mount></div>
    @isset($actions)
        <div class="template-preview-actions">{{ $actions }}</div>
    @endisset
    {{ $slot }}
</div>
