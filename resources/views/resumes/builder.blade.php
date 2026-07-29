@extends('layouts.app', ['title' => 'Resume Builder'])

@section('content')
<div class="builder-header">
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
            <a href="{{ route('resume.templates.show', $resume->template_slug) }}" target="_blank" class="btn btn-outline-secondary builder-preview-button">
                <svg viewBox="0 0 24 24"><path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"/><circle cx="12" cy="12" r="2.5"/></svg>
                Full preview
            </a>
        </div>
    </div>
</div>

<div class="builder-workspace">
    <aside class="builder-steps">
        <div class="builder-progress-copy">
            <span>Resume progress</span>
            <strong>25%</strong>
        </div>
        <div class="progress builder-progress" role="progressbar" aria-label="Resume completion" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
            <div class="progress-bar" style="width: 25%"></div>
        </div>

        <nav class="builder-step-list" aria-label="Resume sections">
            <a href="#personal-details" class="builder-step is-active">
                <span>1</span>
                <div><strong>Personal details</strong><small>Your contact information</small></div>
            </a>
            <span class="builder-step">
                <span>2</span>
                <div><strong>Experience</strong><small>Employment history</small></div>
            </span>
            <span class="builder-step">
                <span>3</span>
                <div><strong>Education</strong><small>Academic background</small></div>
            </span>
            <span class="builder-step">
                <span>4</span>
                <div><strong>Skills & projects</strong><small>Professional strengths</small></div>
            </span>
            <span class="builder-step">
                <span>5</span>
                <div><strong>Review & export</strong><small>Final checks and PDF</small></div>
            </span>
        </nav>
    </aside>

    <main class="builder-editor" id="personal-details">
        <form action="{{ route('resume.builder.update') }}" method="POST" class="builder-form-card">
            @csrf
            @method('PUT')

            <div class="builder-section-heading">
                <span class="builder-section-icon">
                    <svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M5 21a7 7 0 0 1 14 0"/></svg>
                </span>
                <div>
                    <span class="section-kicker">Step 1 of 5</span>
                    <h2>Personal details</h2>
                    <p>Add the information employers should see first.</p>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="full_name" class="form-label">Full name</label>
                    <input id="full_name" name="full_name" class="form-control @error('full_name') is-invalid @enderror" value="{{ old('full_name', $content['full_name']) }}" required>
                    @error('full_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label for="professional_title" class="form-label">Professional title</label>
                    <input id="professional_title" name="professional_title" class="form-control @error('professional_title') is-invalid @enderror" value="{{ old('professional_title', $content['professional_title']) }}" required>
                    @error('professional_title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label for="email" class="form-label">Email address</label>
                    <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $content['email']) }}" required>
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label for="phone" class="form-label">Phone number</label>
                    <input id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $content['phone']) }}" required>
                    @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-12">
                    <label for="location" class="form-label">Location</label>
                    <input id="location" name="location" class="form-control @error('location') is-invalid @enderror" value="{{ old('location', $content['location']) }}" required>
                    @error('location') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label for="linkedin" class="form-label">LinkedIn URL <span>Optional</span></label>
                    <input id="linkedin" name="linkedin" type="url" class="form-control @error('linkedin') is-invalid @enderror" value="{{ old('linkedin', $content['linkedin']) }}" placeholder="https://linkedin.com/in/username">
                    @error('linkedin') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label for="github" class="form-label">GitHub URL <span>Optional</span></label>
                    <input id="github" name="github" type="url" class="form-control @error('github') is-invalid @enderror" value="{{ old('github', $content['github']) }}" placeholder="https://github.com/username">
                    @error('github') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-12">
                    <div class="d-flex justify-content-between">
                        <label for="summary" class="form-label">Professional summary</label>
                        <small class="builder-character-count"><span data-summary-count>{{ strlen(old('summary', $content['summary'])) }}</span>/600</small>
                    </div>
                    <textarea id="summary" name="summary" rows="5" maxlength="600" class="form-control @error('summary') is-invalid @enderror" required data-summary-input>{{ old('summary', $content['summary']) }}</textarea>
                    @error('summary') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="builder-form-footer">
                <span><i></i> Changes are saved when you continue</span>
                <button type="submit" class="btn btn-primary">
                    Save & continue
                    <svg viewBox="0 0 24 24"><path d="m9 18 6-6-6-6"/></svg>
                </button>
            </div>
        </form>
    </main>

    <aside class="builder-live-preview">
        <div class="live-preview-heading">
            <div><span class="section-kicker">Live preview</span><strong>Your resume</strong></div>
            <span>A4</span>
        </div>
        <div class="builder-preview-frame">
            <iframe
                src="{{ route('resume.templates.show', ['template' => $resume->template_slug, 'embed' => 1, 'builder' => 1]) }}"
                title="Selected resume template preview"
            ></iframe>
        </div>
    </aside>
</div>
@endsection
