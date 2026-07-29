@extends('layouts.app', ['title' => 'Resume Builder'])

@section('content')
<div
    class="dynamic-builder"
    data-builder-root
    data-content-url="{{ route('resume.builder.content.update', $resume) }}"
    data-sections-url="{{ route('resume.builder.sections.store', $resume) }}"
    data-reorder-url="{{ route('resume.builder.sections.reorder', $resume) }}"
    data-profile-url="{{ route('resume.profile-image.store', $resume) }}"
>
    <header class="builder-header">
        <div class="container-fluid px-3 px-lg-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <a href="{{ route('dashboard') }}" class="builder-back" aria-label="Back to templates">
                        <svg viewBox="0 0 24 24"><path d="m15 18-6-6 6-6"/></svg>
                    </a>
                    <div>
                        <span class="section-kicker">Resume builder</span>
                        <h1>{{ $template['name'] }}</h1>
                    </div>
                </div>
                <div class="builder-header-actions">
                    <x-builder.save-status />
                    <a href="{{ route('resume.preview', $resume) }}" target="_blank" class="btn btn-outline-secondary builder-preview-button">
                        <svg viewBox="0 0 24 24"><path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"/><circle cx="12" cy="12" r="2.5"/></svg>
                        Full preview
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="enhanced-builder-workspace">
        <main class="dynamic-builder-editor">
            <div class="editor-introduction">
                <div>
                    <span class="section-kicker">Build your story</span>
                    <h2>Resume content</h2>
                    <p>Edit, add, or drag sections into the order that works for you.</p>
                </div>
                <span class="editor-section-count"><b data-section-count>{{ count($builderPayload['sections']) }}</b> sections</span>
            </div>

            <div class="dynamic-section-list" data-section-list></div>

            <button type="button" class="add-custom-section-button" data-add-section>
                <span>
                    <svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                </span>
                <div>
                    <strong>Add custom section</strong>
                    <small>Create a section for publications, volunteering, interests, or anything else.</small>
                </div>
                <svg viewBox="0 0 24 24"><path d="m9 18 6-6-6-6"/></svg>
            </button>
        </main>

        <aside class="builder-live-preview enhanced-preview">
            <div class="live-preview-heading">
                <div><span class="section-kicker">Live preview</span><strong>Your resume</strong></div>
                <div class="preview-tools">
                    <span>A4</span>
                    <button type="button" data-refresh-preview aria-label="Refresh preview">
                        <svg viewBox="0 0 24 24"><path d="M4 12a8 8 0 1 0 2.34-5.66L4 8.68M4 4v4.68h4.68"/></svg>
                    </button>
                </div>
            </div>
            <div class="builder-preview-frame enhanced-frame">
                <div class="preview-loading d-none" data-preview-loading>
                    <span class="spinner-border spinner-border-sm"></span>
                </div>
                <iframe
                    src="{{ $builderPayload['preview_url'] }}"
                    title="Selected resume template preview"
                    data-builder-preview
                ></iframe>
            </div>
        </aside>
    </div>

    <x-builder.add-section-modal />
    <script type="application/json" data-builder-payload>@json($builderPayload)</script>
</div>
@endsection
