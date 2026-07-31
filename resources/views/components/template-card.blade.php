@props([
    'template',
    'mode' => 'create',
    'resume' => null,
    'canCreate' => true,
    'createDisabledMessage' => null,
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
                <i class="fa-solid fa-eye" aria-hidden="true"></i>
                Full preview
            </a>
        </div>
        @if ($mode === 'create')
            <span class="selected-badge">
                <i class="fa-solid fa-check" aria-hidden="true"></i>
                Created
            </span>
        @endif
    </div>
    <div class="template-details">
        <div class="template-card-summary">
            <div class="template-card-flags">
                @if ($template->is_featured === 1)<span>Featured</span>@endif
                @if ($template->is_ats_friendly === 1)<span>ATS friendly</span>@endif
            </div>
            <h3 title="{{ $template->name }}">{{ $template->name }}</h3>
            <p>{{ $template->short_desc }}</p>
        </div>

        @if ($mode === 'switch' && $resume)
            @if ($resume->template_slug === $template->slug)
                <span class="template-create-button is-current" role="img" aria-label="Current template" title="Current template">
                    <i class="fa-solid fa-check" aria-hidden="true"></i>
                </span>
            @else
                <form action="{{ route('resume.template.update', $resume) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="template_slug" value="{{ $template->slug }}">
                    <button
                        type="submit"
                        class="template-create-button template-switch-button"
                        aria-label="Use {{ $template->name }} template"
                        title="Use this template"
                    >
                        <i class="fa-solid fa-right-left" aria-hidden="true"></i>
                        <span class="visually-hidden">Use this template</span>
                    </button>
                </form>
            @endif
        @else
            <button
                type="button"
                class="template-create-button js-select-template"
                data-template="{{ $template->slug }}"
                data-url="{{ route('resume.template.select') }}"
                aria-label="{{ $canCreate ? "Create a resume with {$template->name}" : $createDisabledMessage }}"
                title="{{ $canCreate ? 'Create resume' : $createDisabledMessage }}"
                @disabled(! $canCreate)
                @if (! $canCreate) aria-disabled="true" @endif
            >
                <i class="fa-solid fa-plus template-create-icon" aria-hidden="true"></i>
                <i class="fa-solid fa-check template-created-icon" aria-hidden="true"></i>
                <span class="visually-hidden">Create resume</span>
            </button>
        @endif
    </div>
</article>
