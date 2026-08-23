@php
    $sidebarTypes = ['skills', 'languages', 'education', 'courses'];
@endphp

@foreach ($sections->where('is_visible', true) as $section)
    @continue(! in_array($section->type, $sidebarTypes, true))
    @continue($section->items->isEmpty())

    <section class="template-six-section template-six-side-section template-six-section-{{ $section->type }}">
        <h3>{{ $section->title }}</h3>

        @if ($section->type === 'skills')
            <div class="template-six-skill-list">
                @foreach ($section->items as $item)
                    @php
                        $level = $item->data['level'] ?? null;
                        $percentage = $skillPercentage($level, $loop->index);
                    @endphp
                    <article class="template-six-skill">
                        <div>
                            <span>{{ $item->data['name'] ?? '' }}</span>
                            @if ($level)
                                <small>{{ $level }}</small>
                            @endif
                        </div>
                        <div class="template-six-skill-bar">
                            <i style="--skill-level: {{ $percentage }}%"></i>
                        </div>
                    </article>
                @endforeach
            </div>
        @elseif ($section->type === 'languages')
            <div class="template-six-language-list">
                @foreach ($section->items as $item)
                    @php
                        $level = $item->data['proficiency'] ?? null;
                        $dots = $languageDots($level, $loop->index);
                    @endphp
                    <article>
                        <span>{{ $item->data['name'] ?? '' }}</span>
                        <div aria-label="{{ $level ?: $dots.' out of 5' }}">
                            @for ($dot = 1; $dot <= 5; $dot++)
                                <i class="{{ $dot <= $dots ? 'is-filled' : '' }}"></i>
                            @endfor
                        </div>
                    </article>
                @endforeach
            </div>
        @elseif ($section->type === 'education')
            @foreach ($section->items as $item)
                <article class="template-six-education">
                    <h4>{{ $item->data['degree'] ?? '' }}</h4>
                    @if (filled($item->data['start_date'] ?? null) || filled($item->data['end_date'] ?? null) || filled($item->data['passing_year'] ?? null))
                        <time class="resume-date-range">
                            @include('resumes.partials.experience-date-range', [
                                'startDate' => $item->data['start_date'] ?? null,
                                'endDate' => $item->data['end_date'] ?? null,
                                'passingYear' => $item->data['passing_year'] ?? null,
                                'formatMonth' => $formatMonth,
                            ])
                        </time>
                    @endif
                    <strong>{{ $item->data['institution'] ?? '' }}</strong>
                    @if (filled($item->data['location'] ?? null))
                        <p class="resume-entry-location"><i class="fa-solid fa-location-dot" aria-hidden="true"></i>{{ $item->data['location'] }}</p>
                    @endif
                    @if (filled($item->data['description'] ?? null))
                        <p>{{ $item->data['description'] }}</p>
                    @endif
                </article>
            @endforeach
        @else
            @foreach ($section->items as $item)
                <article class="template-six-education">
                    <h4>{{ $item->data['name'] ?? '' }}</h4>
                    @if (filled($item->data['date'] ?? null))
                        <time>{{ $item->data['date'] }}</time>
                    @endif
                    @if (filled($item->data['provider'] ?? null))
                        <strong>{{ $item->data['provider'] }}</strong>
                    @endif
                    @if (filled($item->data['description'] ?? null))
                        <p>{{ $item->data['description'] }}</p>
                    @endif
                </article>
            @endforeach
        @endif
    </section>
@endforeach
