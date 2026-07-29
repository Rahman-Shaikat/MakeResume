@extends('layouts.app', ['title' => 'Dashboard'])

@section('content')
<div class="dashboard-hero">
    <div class="container-xl">
        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <span class="dashboard-kicker">Your resume workspace</span>
                <h1>Welcome, {{ explode(' ', $user->name)[0] }}.</h1>
                <p>Choose a template, add your profile photo, and start shaping a resume that feels like you.</p>
            </div>
            <div class="col-lg-4">
                <div class="progress-card">
                    <div class="progress-icon">
                        <svg viewBox="0 0 24 24"><path d="M12 3v9l6 3M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Z"/></svg>
                    </div>
                    <div>
                        <span>Resume status</span>
                        <strong>{{ $resume ? 'Ready to customize' : 'Choose a template' }}</strong>
                    </div>
                    <span class="progress-number">{{ $resume ? '35%' : '10%' }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-xl dashboard-content">
    <div class="row g-4">
        <div class="col-xl-8">
            <section class="panel-card">
                <div class="panel-heading">
                    <div>
                        <span class="section-kicker">Step 1</span>
                        <h2>Choose your resume template</h2>
                        <p>Start with a layout designed for clear, confident storytelling.</p>
                    </div>
                    <span class="template-count">{{ count($templates) }} template</span>
                </div>

                <div class="template-grid">
                    @foreach ($templates as $slug => $template)
                        @php($selected = $resume?->template_slug === $slug)
                        <article class="template-card {{ $selected ? 'is-selected' : '' }}" data-template-card="{{ $slug }}">
                            <div class="template-preview">
                                <iframe
                                    src="{{ route('resume.templates.show', ['template' => $slug, 'embed' => 1]) }}"
                                    title="{{ $template['name'] }} preview"
                                    loading="lazy"
                                    tabindex="-1"
                                ></iframe>
                                <div class="template-preview-actions">
                                    <a href="{{ route('resume.templates.show', $slug) }}" target="_blank" class="btn btn-light btn-sm">
                                        <svg viewBox="0 0 24 24"><path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"/><circle cx="12" cy="12" r="2.5"/></svg>
                                        Full preview
                                    </a>
                                </div>
                                <span class="selected-badge">
                                    <svg viewBox="0 0 24 24"><path d="m5 12 4 4L19 6"/></svg>
                                    Selected
                                </span>
                            </div>
                            <div class="template-details">
                                <div>
                                    <h3>{{ $template['name'] }}</h3>
                                    <p>{{ $template['description'] }}</p>
                                </div>
                                <button
                                    type="button"
                                    class="btn {{ $selected ? 'btn-success' : 'btn-primary' }} select-template-button js-select-template"
                                    data-template="{{ $slug }}"
                                    data-url="{{ route('resume.template.select') }}"
                                    @disabled($selected)
                                >
                                    {{ $selected ? 'Selected' : 'Use template' }}
                                </button>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="template-next-bar {{ $resume ? '' : 'd-none' }}" data-template-next>
                    <div>
                        <span class="next-check">
                            <svg viewBox="0 0 24 24"><path d="m5 12 4 4L19 6"/></svg>
                        </span>
                        <div>
                            <strong>Template selected</strong>
                            <small>Continue to add your resume information.</small>
                        </div>
                    </div>
                    <a href="{{ route('resume.builder') }}" class="btn btn-primary next-builder-button">
                        Next
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
                    </a>
                </div>
            </section>
        </div>

        <div class="col-xl-4">
            <section class="panel-card profile-panel">
                <div class="panel-heading compact">
                    <div>
                        <span class="section-kicker">Profile</span>
                        <h2>Add your photo</h2>
                        <p>Shown in the circular profile area of this template.</p>
                    </div>
                </div>

                <form action="{{ route('resume.profile-image.store') }}" method="POST" enctype="multipart/form-data" data-image-form>
                    @csrf
                    <div class="profile-uploader">
                        <div class="profile-preview" data-profile-preview>
                            @if ($resume?->profile_image)
                                <img src="{{ Storage::url($resume->profile_image) }}" alt="{{ $user->name }} profile photo">
                            @else
                                <span>{{ collect(explode(' ', $user->name))->take(2)->map(fn ($part) => strtoupper(substr($part, 0, 1)))->implode('') }}</span>
                            @endif
                        </div>
                        <div>
                            <label for="profile_image" class="btn btn-outline-primary btn-sm upload-trigger">
                                <svg viewBox="0 0 24 24"><path d="M12 16V4m0 0L8 8m4-4 4 4M5 14v6h14v-6"/></svg>
                                Choose image
                            </label>
                            <input id="profile_image" class="visually-hidden" type="file" name="profile_image" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" data-image-input required>
                            <p class="upload-help">JPG, PNG, or WebP. Maximum 2 MB.<br>Use a square image for the best result.</p>
                        </div>
                    </div>

                    @error('profile_image')
                        <div class="alert alert-danger py-2 small">{{ $message }}</div>
                    @enderror

                    <div class="selected-file d-none" data-selected-file>
                        <svg viewBox="0 0 24 24"><path d="M4 4h16v16H4zM4 16l5-5 4 4 2-2 5 5M15 9h.01"/></svg>
                        <span data-file-name></span>
                    </div>

                    <button class="btn btn-primary w-100 mt-3" type="submit" data-upload-button disabled>
                        Save profile photo
                    </button>
                </form>
            </section>

            <section class="panel-card next-steps-panel">
                <span class="section-kicker">Coming next</span>
                <h2>Build your content</h2>
                <div class="next-step">
                    <span>1</span>
                    <div><strong>Personal details</strong><small>Name, contact, and summary</small></div>
                </div>
                <div class="next-step">
                    <span>2</span>
                    <div><strong>Experience & education</strong><small>Your professional story</small></div>
                </div>
                <div class="next-step">
                    <span>3</span>
                    <div><strong>Skills & projects</strong><small>The work that sets you apart</small></div>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection
