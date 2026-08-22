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
<body class="resume-preview-page template-four-preview {{ $embedded ? 'is-embedded' : '' }} {{ request()->boolean('builder') ? 'is-builder-embedded' : '' }}">
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

            return \Illuminate\Support\Carbon::createFromFormat('Y-m', $value)->format('m/Y');
        };
    @endphp

    <main class="resume-canvas">
        <article class="resume-template-four" style="--resume-accent: {{ $resumeTemplate->accent_color }}">
            <div class="template-four-primary">
                <header class="template-four-identity">
                    <div class="template-four-name-row {{ $resumeTemplate->allows_profile_photo === 1 && $resume?->profile_image ? 'has-photo' : '' }}">
                        <div>
                            <h1>{{ $content['full_name'] }}</h1>
                            <h2>{{ $content['professional_title'] }}</h2>
                        </div>
                        @if ($resumeTemplate->allows_profile_photo === 1 && $resume?->profile_image)
                            <div class="template-four-photo">
                                <img src="{{ Storage::url($resume->profile_image) }}" alt="{{ $content['full_name'] }}">
                            </div>
                        @endif
                    </div>

                    <div class="template-four-contact">
                        @if ($content['phone'])
                            <span><b aria-hidden="true">☎</b>{{ $content['phone'] }}</span>
                        @endif
                        <span><b aria-hidden="true">@</b>{{ $content['email'] }}</span>
                        @if ($content['linkedin'])
                            <span><b aria-hidden="true">↗</b>{{ preg_replace('#^https?://(www\.)?#', '', $content['linkedin']) }}</span>
                        @endif
                        @if ($content['website'])
                            <span><b aria-hidden="true">↗</b>{{ preg_replace('#^https?://(www\.)?#', '', $content['website']) }}</span>
                        @endif
                        @if ($content['github'])
                            <span><b aria-hidden="true">↗</b>{{ preg_replace('#^https?://(www\.)?#', '', $content['github']) }}</span>
                        @endif
                        @foreach ($content['social_links'] ?? [] as $socialLink)
                            @if (filled($socialLink['platform'] ?? null) && filled($socialLink['url'] ?? null))
                                <span><b aria-hidden="true">↗</b><a class="resume-social-link" href="{{ $socialLink['url'] }}" target="_blank" rel="noopener">{{ $socialLink['platform'] }}</a></span>
                            @endif
                        @endforeach
                        @if ($content['location'])
                            <span class="template-four-location"><b aria-hidden="true">●</b>{{ $content['location'] }}</span>
                        @endif
                    </div>
                </header>

                @if ($resume)
                    @include('resumes.partials.template-four-main')
                @else
                    @include('resumes.partials.template-four-sample-main')
                @endif
            </div>

            <aside class="template-four-sidebar">
                @if ($resume)
                    @include('resumes.partials.template-four-sidebar')
                @else
                    @include('resumes.partials.template-four-sample-sidebar')
                @endif
            </aside>
        </article>
    </main>
</body>
</html>
