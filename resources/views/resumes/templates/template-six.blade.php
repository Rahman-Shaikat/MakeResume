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
<body class="resume-preview-page template-six-preview {{ $embedded ? 'is-embedded' : '' }} {{ request()->boolean('builder') ? 'is-builder-embedded' : '' }}">
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
                'expert', 'native' => 100,
                'advanced', 'fluent' => 88,
                'intermediate', 'proficient' => 72,
                'beginner', 'basic' => 52,
                default => [100, 100, 80, 100, 100, 100, 80][$index % 7],
            };
        };

        $languageDots = static function (?string $level, int $index = 0): int {
            return match (strtolower((string) $level)) {
                'native', 'expert' => 5,
                'fluent', 'advanced' => 4,
                'proficient', 'intermediate' => 3,
                'basic', 'beginner' => 2,
                default => [5, 4, 3, 3][$index % 4],
            };
        };

        $initials = collect(explode(' ', $content['full_name']))
            ->filter()
            ->take(2)
            ->map(fn (string $part): string => strtoupper(substr($part, 0, 1)))
            ->implode('');
    @endphp

    <main class="resume-canvas">
        <article class="resume-template-six">
            <div class="template-six-primary">
                <header class="template-six-header">
                    <div class="template-six-identity">
                        <h1>{{ $content['full_name'] }}</h1>
                        <h2>{{ $content['professional_title'] }}</h2>
                    </div>
                    <div class="template-six-photo">
                        @if ($resume?->profile_image)
                            <img src="{{ Storage::url($resume->profile_image) }}" alt="{{ $content['full_name'] }}">
                        @else
                            <span>{{ $initials }}</span>
                        @endif
                    </div>
                </header>

                @if ($resume)
                    @include('resumes.partials.template-six-main')
                @else
                    @include('resumes.partials.template-six-sample-main')
                @endif
            </div>

            <aside class="template-six-sidebar">
                <section class="template-six-section template-six-side-section template-six-contact">
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
                                    <strong>{{ $network === 'linkedin' ? 'in' : '↗' }}</strong>
                                    <span>{{ preg_replace('#^https?://(www\.)?#', '', $content[$network]) }}</span>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </section>

                @if ($resume)
                    @include('resumes.partials.template-six-sidebar')
                @else
                    @include('resumes.partials.template-six-sample-sidebar')
                @endif
            </aside>
        </article>
    </main>
</body>
</html>
