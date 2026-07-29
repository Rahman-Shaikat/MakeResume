<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/common/media/favicon.png') }}">
    <title>{{ $content['full_name'] }} - {{ $content['professional_title'] }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="resume-preview-page {{ $embedded ? 'is-embedded' : '' }} {{ request()->boolean('builder') ? 'is-builder-embedded' : '' }}">
    @unless ($embedded)
        <header class="resume-toolbar">
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                <svg viewBox="0 0 24 24"><path d="m15 18-6-6 6-6"/></svg>
                Back to dashboard
            </a>
            <div>
                <span class="toolbar-hint">A4 print-ready preview</span>
                <button type="button" class="btn btn-primary" data-print-resume>
                    <svg viewBox="0 0 24 24"><path d="M6 9V3h12v6M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2M6 14h12v7H6z"/></svg>
                    Print / Save PDF
                </button>
            </div>
        </header>
    @endunless

    <main class="resume-canvas">
        <article class="resume-template-one">
            <header class="resume-top">
                <div class="resume-identity">
                    <h1>{{ strtoupper($content['full_name']) }}</h1>
                    <h2>{{ $content['professional_title'] }}</h2>
                    <div class="resume-contact">
                        <span><b>☎</b>{{ $content['phone'] }}</span>
                        <span><b>@</b>{{ $content['email'] }}</span>
                        @if ($content['linkedin'])
                            <a href="{{ $content['linkedin'] }}" target="_blank"><b>↗</b>{{ $content['linkedin'] }}</a>
                        @endif
                        @if ($content['github'])
                            <a href="{{ $content['github'] }}" target="_blank"><b>↗</b>{{ $content['github'] }}</a>
                        @endif
                        @if ($content['website'])
                            <a href="{{ $content['website'] }}" target="_blank" rel="noopener"><b>↗</b>{{ $content['website'] }}</a>
                        @endif
                        <span class="contact-wide"><b>●</b>{{ $content['location'] }}</span>
                    </div>
                </div>
                <div class="resume-photo">
                    @if ($resume?->profile_image)
                                <img src="{{ Storage::url($resume->profile_image) }}" alt="{{ $content['full_name'] }}">
                    @else
                            <span>{{ collect(explode(' ', $content['full_name']))->take(2)->map(fn ($part) => strtoupper(substr($part, 0, 1)))->implode('') }}</span>
                    @endif
                </div>
            </header>

            @if ($resume)
                @include('resumes.partials.dynamic-sections')
            @else
            <div class="resume-columns">
                <div class="resume-main-column">
                    <section class="resume-section">
                        <h3>Summary</h3>
                        <p>{{ $content['summary'] }}</p>
                    </section>

                    <section class="resume-section experience-section">
                        <h3>Experience</h3>
                        @foreach ($data['experience'] as $experience)
                            <article class="experience-item">
                                <h4>{{ $experience['role'] }}</h4>
                                <h5>{{ $experience['company'] }}</h5>
                                <div class="resume-meta">
                                    <span>▣ {{ $experience['dates'] }}</span>
                                    <span>● {{ $experience['location'] }}</span>
                                    <a href="{{ $experience['url'] }}">↗ {{ preg_replace('#^https?://#', '', $experience['url']) }}</a>
                                </div>
                                <p>{{ $experience['intro'] }}</p>
                                <ul>
                                    @foreach ($experience['highlights'] as $highlight)
                                        <li>{{ $highlight }}</li>
                                    @endforeach
                                </ul>
                            </article>
                        @endforeach
                    </section>

                    <section class="resume-section education-section">
                        <h3>Education</h3>
                        @foreach ($data['education'] as $education)
                            <article class="education-item">
                                <h4>{{ $education['degree'] }}</h4>
                                <h5>{{ $education['school'] }}</h5>
                                <div class="resume-meta">
                                    <span>▣ {{ $education['year'] }}</span>
                                    <span>● {{ $education['location'] }}</span>
                                </div>
                            </article>
                        @endforeach
                    </section>

                    <section class="resume-section course-section">
                        <h3>Training / Courses</h3>
                        <h5>{{ $data['course']['name'] }}</h5>
                        <p>{{ $data['course']['provider'] }}</p>
                    </section>
                </div>

                <aside class="resume-side-column">
                    <section class="resume-section skills-section">
                        <h3>Skills</h3>
                        <div class="resume-skills">
                            @foreach ($data['skills'] as $skill)
                                <span>{{ $skill }}</span>
                            @endforeach
                        </div>
                    </section>

                    <section class="resume-section projects-section">
                        <h3>Projects</h3>
                        @foreach ($data['projects'] as $project)
                            <article class="project-item">
                                <h4>{{ $project['name'] }}</h4>
                                <a href="{{ $project['url'] }}">↗ {{ preg_replace('#^https?://(www\.)?#', '', $project['url']) }}</a>
                                <p>{{ $project['description'] }}</p>
                            </article>
                        @endforeach
                    </section>

                    <section class="resume-section languages-section">
                        <h3>Languages</h3>
                        @foreach ($data['languages'] as $language)
                            <div class="language-item">
                                <div><strong>{{ $language['name'] }}</strong><span>{{ $language['level'] }}</span></div>
                                <div class="language-bar"><i style="width: {{ $language['percent'] }}%"></i></div>
                            </div>
                        @endforeach
                    </section>
                </aside>
            </div>
            @endif
        </article>
    </main>
</body>
</html>
