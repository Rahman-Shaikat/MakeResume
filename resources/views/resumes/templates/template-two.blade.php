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
<body class="resume-preview-page template-two-preview {{ $embedded ? 'is-embedded' : '' }} {{ request()->boolean('builder') ? 'is-builder-embedded' : '' }}">
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
        <article class="resume-template-two" style="--resume-accent: {{ $resumeTemplate->accent_color }}">
            <div class="template-two-primary">
                <header class="template-two-identity {{ $resumeTemplate->allows_profile_photo === 1 && $resume?->profile_image ? 'has-photo' : '' }}">
                    <div>
                        <h1>{{ $content['full_name'] }}</h1>
                        <h2>{{ $content['professional_title'] }}</h2>
                    </div>
                    @if ($resumeTemplate->allows_profile_photo === 1 && $resume?->profile_image)
                        <div class="template-two-photo">
                            <img src="{{ Storage::url($resume->profile_image) }}" alt="{{ $content['full_name'] }}">
                        </div>
                    @endif
                </header>

                @if ($resume)
                    @include('resumes.partials.template-two-main')
                @else
                    @include('resumes.partials.template-two-sample-main')
                @endif
            </div>

            <aside class="template-two-sidebar">
                <section class="template-two-section template-two-contact">
                    <h3>Contact</h3>
                    <ul>
                        @if ($content['location']) <li>{{ $content['location'] }}</li> @endif
                        @if ($content['phone']) <li>{{ $content['phone'] }}</li> @endif
                        <li>{{ $content['email'] }}</li>
                        @if ($content['website']) <li>{{ preg_replace('#^https?://(www\.)?#', '', $content['website']) }}</li> @endif
                        @if ($content['linkedin']) <li>{{ preg_replace('#^https?://(www\.)?#', '', $content['linkedin']) }}</li> @endif
                        @if ($content['github']) <li>{{ preg_replace('#^https?://(www\.)?#', '', $content['github']) }}</li> @endif
                    </ul>
                </section>

                @if ($resume)
                    @include('resumes.partials.template-two-sidebar')
                @else
                    @include('resumes.partials.template-two-sample-sidebar')
                @endif
            </aside>
        </article>
    </main>
</body>
</html>
