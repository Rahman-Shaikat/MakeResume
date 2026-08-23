@php
    $mainTypes = ['summary', 'experience', 'education'];
@endphp

@foreach ($sections->where('is_visible', true) as $section)
    @continue(! in_array($section->type, $mainTypes, true))

    @php
        $items = $section->items;
        $hasContent = $section->type === 'summary'
            ? filled($content['summary'] ?? null)
            : $items->isNotEmpty();
    @endphp

    @continue(! $hasContent)

    <section class="template-four-section template-four-main-section template-four-section-{{ $section->type }}">
        <h3>{{ $section->title }}</h3>

        @if ($section->type === 'summary')
            <p class="template-four-summary">{{ $content['summary'] }}</p>
        @elseif ($section->type === 'experience')
            @foreach ($items as $item)
                <article class="template-four-experience">
                    <div class="template-four-entry-row">
                        <h4>{{ $item->data['title'] ?? '' }}</h4>
                        @if (filled($item->data['start_date'] ?? null) || filled($item->data['end_date'] ?? null))
                            <time class="resume-date-range">
                                @include('resumes.partials.experience-date-range', [
                                    'startDate' => $item->data['start_date'] ?? null,
                                    'endDate' => $item->data['end_date'] ?? null,
                                    'isCurrent' => $item->data['current'] ?? false,
                                    'formatMonth' => $formatMonth,
                                ])
                            </time>
                        @endif
                    </div>
                    <div class="template-four-entry-row template-four-organization-row">
                        <strong>
                            @if (filled($item->data['company_website'] ?? null))
                                <a class="resume-company-link" href="{{ $item->data['company_website'] }}" target="_blank" rel="noopener">{{ $item->data['company'] ?? '' }}</a>
                            @else
                                {{ $item->data['company'] ?? '' }}
                            @endif
                        </strong>
                        @if (filled($item->data['location'] ?? null))
                            <span class="resume-entry-location"><i class="fa-solid fa-location-dot" aria-hidden="true"></i>{{ $item->data['location'] }}</span>
                        @endif
                    </div>
                    @if (filled($item->data['description'] ?? null))
                        <ul>
                            @foreach (preg_split('/\r\n|\r|\n/', $item->data['description']) as $achievement)
                                @if (filled($achievement))
                                    <li>{{ $achievement }}</li>
                                @endif
                            @endforeach
                        </ul>
                    @endif
                </article>
            @endforeach
        @else
            @foreach ($items as $item)
                <article class="template-four-education">
                    <div class="template-four-entry-row">
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
                    </div>
                    <div class="template-four-entry-row template-four-organization-row">
                        <strong>{{ $item->data['institution'] ?? '' }}</strong>
                        @if (filled($item->data['location'] ?? null))
                            <span class="resume-entry-location"><i class="fa-solid fa-location-dot" aria-hidden="true"></i>{{ $item->data['location'] }}</span>
                        @endif
                    </div>
                    @if (filled($item->data['description'] ?? null))
                        <p>{{ $item->data['description'] }}</p>
                    @endif
                </article>
            @endforeach
        @endif
    </section>
@endforeach
