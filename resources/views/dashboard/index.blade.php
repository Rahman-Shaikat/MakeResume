@extends('layouts.app', ['title' => 'Dashboard'])

@section('content')
<div class="dashboard-hero">
    <div class="container-xl">
        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <span class="dashboard-kicker">Your resume workspace</span>
                <h1>Welcome, {{ explode(' ', $user->name)[0] }}.</h1>
                <p>Continue a saved resume or create a new version for your next opportunity.</p>
            </div>
            <div class="col-lg-4">
                <div class="progress-card">
                    <div class="progress-icon">
                        <svg viewBox="0 0 24 24"><path d="M5 4h14v16H5zM8 8h8M8 12h8M8 16h5"/></svg>
                    </div>
                    <div>
                        <span>Your workspace</span>
                        <strong>{{ $resumes->isEmpty() ? 'Create your first resume' : 'Resumes ready to edit' }}</strong>
                    </div>
                    <span class="progress-number">{{ $resumes->count() }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-xl dashboard-content">
    <section class="panel-card saved-resumes-panel" id="saved-resumes">
        <div class="panel-heading">
            <div>
                <span class="section-kicker">Your work</span>
                <h2>Previously saved resumes</h2>
                <p>Select a resume to continue editing exactly where you left off.</p>
            </div>
            <a href="#resume-templates" class="btn btn-primary create-resume-link">
                <svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                Create new resume
            </a>
        </div>

        @if ($resumes->isEmpty())
            <div class="saved-resumes-empty">
                <span><svg viewBox="0 0 24 24"><path d="M7 3h7.5L19 7.5V21H7zM14.5 3v4.5H19M10 12h6M10 16h6"/></svg></span>
                <h3>No saved resumes yet</h3>
                <p>Choose a template below to create your first professional resume.</p>
                <a href="#resume-templates" class="btn btn-outline-primary">Browse templates</a>
            </div>
        @else
            <div class="saved-resume-grid">
                @foreach ($resumes as $resume)
                    @php
                        $resumeContent = $resume->content ?? [];
                        $template = $resume->template;
                        $templateName = $template?->name ?? str($resume->template_slug)->headline();
                        $resumeTitle = $resumeContent['professional_title'] ?? $templateName.' Resume';
                        $resumeOwner = $resumeContent['full_name'] ?? $user->name;
                    @endphp
                    <article class="saved-resume-card">
                        <form
                            method="POST"
                            action="{{ route('resume.destroy', $resume) }}"
                            class="saved-resume-delete-form"
                            data-delete-resume-form
                            data-resume-title="{{ $resumeTitle }}"
                        >
                            @csrf
                            @method('DELETE')
                            <button
                                type="submit"
                                class="saved-resume-delete-button"
                                aria-label="Delete {{ $resumeTitle }}"
                                title="Delete resume"
                            >
                                <svg viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M4 7h16M9 7V4h6v3M7 7l1 13h8l1-13M10 11v5M14 11v5"/>
                                </svg>
                            </button>
                        </form>

                        <div class="saved-resume-paper" aria-hidden="true">
                            <div class="saved-paper-header">
                                <div>
                                    <i></i><i></i>
                                </div>
                                <span>
                                    @if ($resume->profile_image)
                                        <img src="{{ Storage::url($resume->profile_image) }}" alt="">
                                    @else
                                        {{ strtoupper(substr($resumeOwner, 0, 1)) }}
                                    @endif
                                </span>
                            </div>
                            <div class="saved-paper-columns">
                                <div><b></b><i></i><i></i><i></i><b></b><i></i><i></i></div>
                                <div><b></b><i></i><i></i><b></b><i></i></div>
                            </div>
                            <small>Resume {{ $loop->iteration }}</small>
                        </div>

                        <div class="saved-resume-content">
                            <div class="saved-resume-copy">
                                <span>{{ $templateName }}</span>
                                <h3>{{ $resumeTitle }}</h3>
                                <p>{{ $resumeOwner }}</p>
                                <small>Updated {{ $resume->updated_at->diffForHumans() }}</small>
                            </div>
                            <div class="saved-resume-actions">
                                <a href="{{ route('resume.builder', $resume) }}" class="btn btn-primary">
                                    Edit resume
                                    <svg viewBox="0 0 24 24"><path d="m9 18 6-6-6-6"/></svg>
                                </a>
                                <a href="{{ route('resume.templates.index', $resume) }}" class="btn btn-light" aria-label="Change template for {{ $resumeTitle }}">
                                    Change template
                                </a>
                                <a href="{{ route('resume.preview', $resume) }}" target="_blank" class="btn btn-light" aria-label="Preview {{ $resumeTitle }}">
                                    <svg viewBox="0 0 24 24"><path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"/><circle cx="12" cy="12" r="2.5"/></svg>
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>

    <div class="row g-4" id="resume-templates">
        <div class="col-xl-8">
            <section class="panel-card" data-template-slider>
                <div class="panel-heading">
                    <div>
                        <span class="section-kicker">Create new</span>
                        <h2>Choose a resume template</h2>
                        <p>Each selection creates a separate resume in your workspace.</p>
                    </div>
                    <div class="template-catalog-tools">
                        <form action="{{ route('dashboard') }}#resume-templates" method="GET">
                            <label class="visually-hidden" for="template-category">Filter templates by category</label>
                            <select id="template-category" name="category" class="form-select" onchange="this.form.submit()">
                                <option value="">All templates</option>
                                @foreach ($templateCategories as $category)
                                    <option value="{{ $category->slug }}" @selected($selectedCategory === $category->slug)>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </form>
                        <span class="template-count">{{ $templates->count() }} {{ Str::plural('template', $templates->count()) }}</span>
                    </div>
                </div>

                <div class="template-slider">
                    @if ($templates->count() > 1)
                        <div class="template-slider-controls" role="group" aria-label="Resume template navigation">
                            <button
                                type="button"
                                data-template-slider-previous
                                aria-label="Show previous templates"
                                aria-controls="resume-template-slider"
                                disabled
                            >
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
                            </button>
                            <button
                                type="button"
                                data-template-slider-next
                                aria-label="Show next templates"
                                aria-controls="resume-template-slider"
                            >
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
                            </button>
                        </div>
                    @endif

                    <div
                        class="template-grid"
                        id="resume-template-slider"
                        data-template-slider-track
                        tabindex="0"
                        aria-label="Resume templates"
                    >
                        @forelse ($templates as $template)
                            <x-template-card :template="$template" />
                        @empty
                            <div class="template-empty-state">
                                <h3>No active templates in this category</h3>
                                <p>Choose “All templates” to browse the complete catalog.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="template-next-bar d-none" data-template-next>
                    <div>
                        <span class="next-check">
                            <svg viewBox="0 0 24 24"><path d="m5 12 4 4L19 6"/></svg>
                        </span>
                        <div>
                            <strong>New resume created</strong>
                            <small>Continue to add your resume information.</small>
                        </div>
                    </div>
                    <a href="#" class="btn btn-primary next-builder-button" data-builder-link>
                        Open builder
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
                    </a>
                </div>
            </section>
        </div>

        <div class="col-xl-4">
            <section class="panel-card workspace-summary-panel">
                <span class="section-kicker">Workspace</span>
                <h2>One resume for every goal</h2>
                <p>Create tailored versions without overwriting the resumes you have already saved.</p>

                <div class="workspace-stat">
                    <span><svg viewBox="0 0 24 24"><path d="M7 3h7.5L19 7.5V21H7zM14.5 3v4.5H19"/></svg></span>
                    <div><strong>{{ $resumes->count() }}</strong><small>Saved {{ Str::plural('resume', $resumes->count()) }}</small></div>
                </div>
                <div class="workspace-stat">
                    <span><svg viewBox="0 0 24 24"><path d="M12 3v9l6 3M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Z"/></svg></span>
                    <div>
                        <strong>{{ $resumes->first()?->updated_at?->diffForHumans() ?? 'Not yet' }}</strong>
                        <small>Last edited</small>
                    </div>
                </div>

                <div class="workspace-tip">
                    <svg viewBox="0 0 24 24"><path d="M9 18h6M10 22h4M8 14a6 6 0 1 1 8 0c-1 1-1 2-1 2H9s0-1-1-2Z"/></svg>
                    <p><strong>Tip:</strong> Use a focused resume for each role or industry you apply to.</p>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection
