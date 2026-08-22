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
<body class="resume-preview-page template-five-preview {{ $embedded ? 'is-embedded' : '' }} {{ request()->boolean('builder') ? 'is-builder-embedded' : '' }}">
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

    @php
        $formatMonth = static function (?string $value): string {
            if (! $value) {
                return '';
            }

            return \Illuminate\Support\Carbon::createFromFormat('Y-m', $value)->format('M Y');
        };

        $skillPercentage = static function (?string $level, int $index = 0): int {
            return match (strtolower((string) $level)) {
                'expert', 'native' => 95,
                'advanced', 'fluent' => 85,
                'intermediate', 'proficient' => 70,
                'beginner', 'basic' => 50,
                default => [80, 78, 84, 62, 82][$index % 5],
            };
        };

        $initials = collect(explode(' ', $content['full_name']))
            ->filter()
            ->take(2)
            ->map(fn (string $part): string => strtoupper(substr($part, 0, 1)))
            ->implode('');
    @endphp

    <main class="resume-canvas">
        <article class="resume-template-five" style="--resume-accent: {{ $resumeTemplate->accent_color }}">
            <header class="template-five-header {{ $resumeTemplate->allows_profile_photo === 1 ? '' : 'without-photo' }}">
                @if ($resumeTemplate->allows_profile_photo === 1)
                    <div class="template-five-photo">
                        @if ($resume?->profile_image)
                            <img src="{{ Storage::url($resume->profile_image) }}" alt="{{ $content['full_name'] }}">
                        @else
                            <span>{{ $initials }}</span>
                        @endif
                    </div>
                @endif
                <div class="template-five-identity">
                    <h1>{{ $content['full_name'] }}</h1>
                    <h2>{{ $content['professional_title'] }}</h2>
                </div>
            </header>

            <div class="template-five-columns">
                <aside class="template-five-sidebar">
                    <section class="template-five-section template-five-contact">
                        <h3>Contact</h3>
                        <ul>
                            <li>
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m3 6 9 7 9-7M4 5h16v14H4z"/></svg>
                                <span>{{ $content['email'] }}</span>
                            </li>
                            @if ($content['phone'])
                                <li>
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 3h10v18H7zM10 18h4"/></svg>
                                    <span>{{ $content['phone'] }}</span>
                                </li>
                            @endif
                            @if ($content['location'])
                                <li>
                                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/></svg>
                                    <span>{{ $content['location'] }}</span>
                                </li>
                            @endif
                            @foreach (['linkedin', 'website', 'github'] as $network)
                                @if ($content[$network])
                                    <li>
                                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M10 14a5 5 0 0 0 7 0l3-3a5 5 0 0 0-7-7l-2 2M14 10a5 5 0 0 0-7 0l-3 3a5 5 0 0 0 7 7l2-2"/></svg>
                                        <span>{{ preg_replace('#^https?://(www\.)?#', '', $content[$network]) }}</span>
                                    </li>
                                @endif
                            @endforeach
                            @foreach ($content['social_links'] ?? [] as $socialLink)
                                @if (filled($socialLink['platform'] ?? null) && filled($socialLink['url'] ?? null))
                                    <li>
                                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M10 14a5 5 0 0 0 7 0l3-3a5 5 0 0 0-7-7l-2 2M14 10a5 5 0 0 0-7 0l-3 3a5 5 0 0 0 7 7l2-2"/></svg>
                                        <a class="resume-social-link" href="{{ $socialLink['url'] }}" target="_blank" rel="noopener">{{ $socialLink['platform'] }}</a>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </section>

                    @if ($resume)
                        @include('resumes.partials.template-five-sidebar')
                    @else
                        @include('resumes.partials.template-five-sample-sidebar')
                    @endif
                </aside>

                <div class="template-five-primary">
                    @if ($resume)
                        @include('resumes.partials.template-five-main')
                    @else
                        @include('resumes.partials.template-five-sample-main')
                    @endif
                </div>
            </div>
        </article>
    </main>
</body>
</html>
