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
<body class="resume-preview-page temp-two-preview {{ $embedded ? 'is-embedded' : '' }} {{ request()->boolean('builder') ? 'is-builder-embedded' : '' }}">
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
        <article class="resume-temp-two">
            <aside class="temp-two-sidebar">
                <section class="temp-two-section temp-two-contact">
                    @include('resumes.partials.temp-two-heading', ['type' => 'contact', 'title' => 'Contacts'])
                    <ul>
                        @if ($content['phone'])
                            <li><span aria-hidden="true">☎</span>{{ $content['phone'] }}</li>
                        @endif
                        <li><span aria-hidden="true">@</span>{{ $content['email'] }}</li>
                        @if ($content['linkedin'])
                            <li><span aria-hidden="true">↗</span>{{ preg_replace('#^https?://(www\.)?#', '', $content['linkedin']) }}</li>
                        @endif
                        @if ($content['website'])
                            <li><span aria-hidden="true">↗</span>{{ preg_replace('#^https?://(www\.)?#', '', $content['website']) }}</li>
                        @endif
                        @if ($content['github'])
                            <li><span aria-hidden="true">↗</span>{{ preg_replace('#^https?://(www\.)?#', '', $content['github']) }}</li>
                        @endif
                        @if ($content['location'])
                            <li><span aria-hidden="true">●</span>{{ $content['location'] }}</li>
                        @endif
                    </ul>
                </section>

                @if ($resume)
                    @include('resumes.partials.temp-two-sidebar')
                @else
                    @include('resumes.partials.temp-two-sample-sidebar')
                @endif
            </aside>

            <div class="temp-two-primary">
                <header class="temp-two-identity">
                    <div class="temp-two-name-row {{ $resume?->profile_image ? 'has-photo' : '' }}">
                        <h1>{{ $content['full_name'] }}</h1>
                        @if ($resume?->profile_image)
                            <div class="temp-two-photo">
                                <img src="{{ Storage::url($resume->profile_image) }}" alt="{{ $content['full_name'] }}">
                            </div>
                        @endif
                    </div>
                    <p>{{ $content['professional_title'] }}</p>
                </header>

                @if ($resume)
                    @include('resumes.partials.temp-two-main')
                @else
                    @include('resumes.partials.temp-two-sample-main')
                @endif
            </div>
        </article>
    </main>
</body>
</html>
