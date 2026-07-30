@props([
    'template',
    'mode' => 'create',
    'resume' => null,
])

<article class="template-card" data-template-card="{{ $template->slug }}">
    <div
        class="template-preview"
        style="--resume-accent: {{ $template->accent_color }}"
        data-live-template-preview
        data-live-preview-url="{{ route('resume.templates.show', ['template' => $template->slug, 'embed' => 1]) }}"
        data-live-preview-title="{{ $template->name }} live preview"
        data-preview-state="idle"
        aria-busy="true"
    >
        <div class="template-preview-fallback" data-template-preview-fallback>
            @if ($template->thumbnailUrl())
                <img
                    src="{{ $template->thumbnailUrl() }}"
                    alt="{{ $template->name }} resume thumbnail"
                    loading="lazy"
                    width="700"
                    height="990"
                >
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
        <div class="template-preview-actions">
            <a href="{{ route('resume.templates.show', $template->slug) }}" target="_blank" rel="noopener" class="btn btn-light btn-sm">
                <svg viewBox="0 0 24 24"><path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"/><circle cx="12" cy="12" r="2.5"/></svg>
                Full preview
            </a>
        </div>
        @if ($mode === 'create')
            <span class="selected-badge">
                <svg viewBox="0 0 24 24"><path d="m5 12 4 4L19 6"/></svg>
                Created
            </span>
        @endif
    </div>
    <div class="template-details">
        <div>
            <div class="template-card-flags">
                @if ($template->is_featured === 1)<span>Featured</span>@endif
                @if ($template->is_ats_friendly === 1)<span>ATS friendly</span>@endif
            </div>
            <h3>{{ $template->name }}</h3>
            <p>{{ $template->short_desc }}</p>
        </div>

        @if ($mode === 'switch' && $resume)
            @if ($resume->template_slug === $template->slug)
                <button type="button" class="btn btn-light" disabled>Current template</button>
            @else
                <form action="{{ route('resume.template.update', $resume) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="template_slug" value="{{ $template->slug }}">
                    <button type="submit" class="btn btn-primary">Use this template</button>
                </form>
            @endif
        @else
            <button
                type="button"
                class="btn btn-primary select-template-button js-select-template"
                data-template="{{ $template->slug }}"
                data-url="{{ route('resume.template.select') }}"
            >
                Create resume
            </button>
        @endif
    </div>
</article>
