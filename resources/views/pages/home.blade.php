@extends('layouts.marketing', ['title' => 'Resume Engineer - Build a resume that moves you forward'])

@section('content')
    @php
        $primaryUrl = auth()->check() ? route('dashboard') : route('register');
        $primaryLabel = auth()->check() ? 'Open my workspace' : 'Create my resume';
        $heroTemplate = $hero?->resumeTemplate ?? $templates->first();
        $heroPreview = $hero?->previewUrl()
            ?? $heroTemplate?->thumbnailUrl()
            ?? asset('assets/resume-templates/template-one.png');
        $productPreview = $templates->get(1)?->thumbnailUrl() ?? asset('assets/resume-templates/template-three.png');
        $heroEyebrow = $hero?->eyebrow ?? 'Your next role starts here';
        $heroHeadline = $hero?->headline ?? 'Build a resume that makes your next move feel possible.';
        $heroDescription = $hero?->description ?? 'Choose a thoughtful template, shape every detail around your experience, and see a polished resume take form as you work.';
        $heroTopBadge = $hero?->top_badge ?? 'Designed around you';
        $heroEditorTitle = $hero?->editor_title ?? 'Professional summary';
        $heroEditorDescription = $hero?->editor_description ?? 'Clear, focused, and ready for the role you want next.';
        $heroBottomTitle = $hero?->bottom_status_title ?? 'Saved automatically';
        $heroBottomText = $hero?->bottom_status_text ?? 'Your progress is safe';
        $showcaseEyebrow = $showcase?->eyebrow ?? 'Designed to be read';
        $showcaseHeadline = $showcase?->headline ?? 'Choose a template that puts your experience in focus.';
        $showcaseCtaLabel = $showcase?->cta_label ?? 'Explore your workspace';
    @endphp

    <section class="home-hero">
        <div class="container-xl home-hero-grid">
            <div class="home-hero-copy" data-reveal>
                <span class="home-eyebrow"><i class="fa-solid fa-sparkles" aria-hidden="true"></i> {{ $heroEyebrow }}</span>
                <h1>{{ $heroHeadline }}</h1>
                <p>{{ $heroDescription }}</p>
                <div class="home-hero-actions">
                    <a href="{{ $primaryUrl }}" class="btn btn-primary home-primary-cta">
                        {{ $primaryLabel }} <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                    </a>
                    <a href="#templates" class="home-secondary-cta">View templates <i class="fa-solid fa-arrow-down" aria-hidden="true"></i></a>
                </div>
                <div class="home-hero-reassurance" aria-label="Resume Engineer benefits">
                    <span><i class="fa-solid fa-circle-check" aria-hidden="true"></i> Start in minutes</span>
                    <span><i class="fa-solid fa-circle-check" aria-hidden="true"></i> Save and refine anytime</span>
                </div>
            </div>

            <div class="home-hero-visual" data-reveal data-reveal-delay="1">
                <div class="home-visual-glow"></div>
                <div class="home-status-card home-status-card-top"><i class="fa-solid fa-wand-magic-sparkles" aria-hidden="true"></i><span>{{ $heroTopBadge }}</span></div>
                <div class="home-resume-paper">
                    <img src="{{ $heroPreview }}" width="700" height="990" alt="A Resume Engineer template preview" fetchpriority="high">
                </div>
                <div class="home-editor-card">
                    <div class="home-editor-card-head"><span><i class="fa-solid fa-pen" aria-hidden="true"></i></span><strong>{{ $heroEditorTitle }}</strong><i class="fa-solid fa-ellipsis" aria-hidden="true"></i></div>
                    <p>{{ $heroEditorDescription }}</p>
                    <div><i></i><i></i><i></i></div>
                </div>
                <div class="home-status-card home-status-card-bottom"><span class="home-status-check"><i class="fa-solid fa-check" aria-hidden="true"></i></span><div><strong>{{ $heroBottomTitle }}</strong><small>{{ $heroBottomText }}</small></div></div>
            </div>
        </div>
    </section>

    <section class="home-trust-strip">
        <div class="container-xl home-trust-list">
            <span><i class="fa-solid fa-compass-drafting" aria-hidden="true"></i> Professional templates</span>
            <span><i class="fa-solid fa-pen-to-square" aria-hidden="true"></i> Easy editing</span>
            <span><i class="fa-solid fa-eye" aria-hidden="true"></i> Live preview</span>
            <span><i class="fa-solid fa-list-check" aria-hidden="true"></i> Flexible sections</span>
            <span><i class="fa-solid fa-print" aria-hidden="true"></i> Print-ready layout</span>
            <span><i class="fa-solid fa-heart" aria-hidden="true"></i> Beginner-friendly</span>
        </div>
    </section>

    <section class="container-xl home-section home-introduction" id="how-it-works">
        <div class="home-section-heading" data-reveal>
            <span class="section-kicker">A focused way to begin</span>
            <h2>From blank page to confident application in three clear steps.</h2>
            <p>Resume Engineer keeps the process simple while giving you the control to make every version feel like yours.</p>
        </div>

        <div class="home-steps">
            <article data-reveal data-reveal-delay="1">
                <span class="home-step-number">01</span>
                <span class="home-step-icon"><i class="fa-solid fa-swatchbook" aria-hidden="true"></i></span>
                <h3>Choose a template</h3>
                <p>Start with a professional structure that fits the way you want to present your experience.</p>
            </article>
            <article data-reveal data-reveal-delay="2">
                <span class="home-step-number">02</span>
                <span class="home-step-icon"><i class="fa-solid fa-pen-ruler" aria-hidden="true"></i></span>
                <h3>Add your details</h3>
                <p>Build each section at your pace, then tailor a version for every role you are pursuing.</p>
            </article>
            <article data-reveal data-reveal-delay="3">
                <span class="home-step-number">03</span>
                <span class="home-step-icon"><i class="fa-solid fa-file-circle-check" aria-hidden="true"></i></span>
                <h3>Preview and prepare</h3>
                <p>Review the final layout, make small adjustments, and prepare a resume ready to share.</p>
            </article>
        </div>
    </section>

    <section class="home-template-section" id="templates">
        <div class="container-xl">
            <div class="home-template-header" data-reveal>
                <div>
                    <span class="section-kicker">{{ $showcaseEyebrow }}</span>
                    <h2>{{ $showcaseHeadline }}</h2>
                </div>
                <a href="{{ $primaryUrl }}" class="text-link">{{ $showcaseCtaLabel }} <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
            </div>

            <div class="home-template-grid">
                @forelse ($templates as $template)
                    <article class="home-template-card" data-reveal>
                        <div class="home-template-image" style="--template-accent: {{ $template->accent_color }}">
                            <img src="{{ $template->thumbnailUrl() ?? asset('assets/resume-templates/template-one.png') }}" width="700" height="990" alt="{{ $template->name }} resume template" loading="lazy">
                            <span>{{ $template->is_ats_friendly === 1 ? 'ATS friendly' : 'Professional layout' }}</span>
                        </div>
                        <div class="home-template-copy">
                            <div><h3>{{ $template->name }}</h3><p>{{ $template->short_desc }}</p></div>
                            <a href="{{ $primaryUrl }}" aria-label="Use {{ $template->name }}"><i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                        </div>
                    </article>
                @empty
                    @foreach ([
                        ['Professional Cyan', 'A crisp two-column layout for focused, polished applications.', 'assets/resume-templates/template-one.png'],
                        ['Classic Blue Sidebar', 'A refined engineering resume with a spacious experience column and structured blue sidebar.', 'assets/resume-templates/template-two.png'],
                        ['Modern Mint Professional', 'A clean modern structure that keeps key experience easy to scan.', 'assets/resume-templates/template-three.png'],
                        ['Teal Impact', 'A confident sidebar layout for a memorable professional story.', 'assets/resume-templates/template-four.png'],
                    ] as [$name, $description, $thumbnail])
                        <article class="home-template-card" data-reveal>
                            <div class="home-template-image">
                                <img src="{{ asset($thumbnail) }}" width="700" height="990" alt="{{ $name }} resume template" loading="lazy">
                                <span>Professional layout</span>
                            </div>
                            <div class="home-template-copy">
                                <div><h3>{{ $name }}</h3><p>{{ $description }}</p></div>
                                <a href="{{ $primaryUrl }}" aria-label="Use {{ $name }}"><i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                            </div>
                        </article>
                    @endforeach
                @endforelse
            </div>
        </div>
    </section>

    <section class="container-xl home-product-section">
        <div class="home-product-preview" data-reveal>
            <div class="home-product-window">
                <div class="home-product-topbar"><span></span><span></span><span></span><strong>Your resume</strong><i class="fa-solid fa-cloud-arrow-up" aria-hidden="true"></i></div>
                <div class="home-product-workspace">
                    <div class="home-product-editor">
                        <div class="home-editor-progress"><span>Resume essentials</span><strong>4 of 5 complete</strong><i><b></b></i></div>
                        <article><span><i class="fa-solid fa-user" aria-hidden="true"></i></span><div><strong>Personal details</strong><small>Tell employers who you are</small></div><i class="fa-solid fa-check" aria-hidden="true"></i></article>
                        <article><span><i class="fa-solid fa-briefcase" aria-hidden="true"></i></span><div><strong>Work experience</strong><small>Show the impact you made</small></div><i class="fa-solid fa-chevron-down" aria-hidden="true"></i></article>
                        <article><span><i class="fa-solid fa-lightbulb" aria-hidden="true"></i></span><div><strong>Skills &amp; strengths</strong><small>Add what sets you apart</small></div><i class="fa-solid fa-chevron-down" aria-hidden="true"></i></article>
                        <span class="home-product-add-section"><i class="fa-solid fa-plus" aria-hidden="true"></i> Add a custom section</span>
                    </div>
                    <div class="home-product-paper"><img src="{{ $productPreview }}" width="700" height="990" alt="Resume preview updating alongside editor" loading="lazy"></div>
                </div>
            </div>
        </div>
        <div class="home-product-copy" data-reveal data-reveal-delay="1">
            <span class="section-kicker">A workspace that stays out of your way</span>
            <h2>See the story take shape as you build it.</h2>
            <p>Move through your resume one focused section at a time while the live preview keeps the big picture visible. Add, reorder, or refine details without starting over.</p>
            <ul>
                <li><i class="fa-solid fa-check" aria-hidden="true"></i><span><strong>Live preview</strong> keeps formatting decisions clear.</span></li>
                <li><i class="fa-solid fa-check" aria-hidden="true"></i><span><strong>Custom sections</strong> make room for the work that matters.</span></li>
                <li><i class="fa-solid fa-check" aria-hidden="true"></i><span><strong>Saved versions</strong> let you tailor without losing progress.</span></li>
            </ul>
        </div>
    </section>

    <section class="home-feature-section">
        <div class="container-xl">
            <div class="home-feature-heading" data-reveal>
                <span class="section-kicker">Built for real career moves</span>
                <h2>Everything you need to make the details feel considered.</h2>
            </div>
            <div class="home-feature-grid">
                <article data-reveal><i class="fa-solid fa-table-cells-large" aria-hidden="true"></i><h3>Dynamic sections</h3><p>Build a resume that reflects the shape of your real experience.</p></article>
                <article data-reveal><i class="fa-solid fa-arrow-down-up-across-line" aria-hidden="true"></i><h3>Flexible ordering</h3><p>Put your strongest information exactly where it earns attention.</p></article>
                <article data-reveal><i class="fa-solid fa-square-plus" aria-hidden="true"></i><h3>Custom sections</h3><p>Add projects, languages, certifications, and more when they matter.</p></article>
                <article data-reveal><i class="fa-solid fa-image-portrait" aria-hidden="true"></i><h3>Profile image control</h3><p>Use a photo when it fits the role and the template supports it.</p></article>
                <article data-reveal><i class="fa-solid fa-eye" aria-hidden="true"></i><h3>Live layout preview</h3><p>Review the design as you write, not after you have finished.</p></article>
                <article data-reveal><i class="fa-solid fa-cloud" aria-hidden="true"></i><h3>Save and return</h3><p>Keep every tailored version ready to edit whenever an opportunity appears.</p></article>
            </div>
        </div>
    </section>

    <section class="container-xl home-final-cta" data-reveal>
        <div>
            <span class="home-eyebrow"><i class="fa-solid fa-rocket" aria-hidden="true"></i> Ready when you are</span>
            <h2>Your next opportunity deserves a clear, confident introduction.</h2>
            <p>Start with a thoughtful template and turn your experience into a resume you feel ready to send.</p>
        </div>
        <a href="{{ $primaryUrl }}" class="btn btn-light">{{ $primaryLabel }} <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
    </section>
@endsection
