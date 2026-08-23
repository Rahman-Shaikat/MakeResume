@php
    $sidebarTypes = ['personal', 'skills', 'education', 'courses', 'awards', 'languages'];
@endphp

@foreach ($sections->where('is_visible', true) as $section)
    @continue(in_array($section->type, $sidebarTypes, true))

    @php
        $items = $section->items;
        $hasContent = $section->type === 'summary'
            ? filled($content['summary'] ?? null)
            : $items->isNotEmpty();
    @endphp

    @continue(! $hasContent)

    <section class="template-two-section template-two-main-section template-two-section-{{ $section->type }}">
        <h3>{{ $section->title }}</h3>

        @if ($section->type === 'summary')
            <p class="template-two-summary">{{ $content['summary'] }}</p>
        @elseif ($section->type === 'experience')
            @foreach ($items as $item)
                <article class="template-two-experience">
                    <p class="template-two-employer">
                        <strong>
                            @if (filled($item->data['company_website'] ?? null))
                                <a class="resume-company-link" href="{{ $item->data['company_website'] }}" target="_blank" rel="noopener">{{ $item->data['company'] ?? '' }}</a>
                            @else
                                {{ $item->data['company'] ?? '' }}
                            @endif
                        </strong>
                        @if (filled($item->data['location'] ?? null))
                            <span class="resume-entry-location">, <i class="fa-solid fa-location-dot" aria-hidden="true"></i>{{ $item->data['location'] }}</span>
                        @endif
                    </p>
                    <div class="template-two-role-row">
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
                    @if (filled($item->data['description'] ?? null))
                        <ul class="template-two-achievements">
                            @foreach (preg_split('/\r\n|\r|\n/', $item->data['description']) as $achievement)
                                @if (filled($achievement)) <li>{{ $achievement }}</li> @endif
                            @endforeach
                        </ul>
                    @endif
                </article>
            @endforeach
        @elseif ($section->type === 'projects')
            @foreach ($items as $item)
                <article @class(['template-two-project', 'project-without-description' => ! filled($item->data['description'] ?? null)])>
                    <div class="template-two-project-heading">
                        <h4>
                            {{ $item->data['name'] ?? '' }}@if (filled($item->data['project_domain'] ?? null)) <span class="resume-project-domain">({{ $item->data['project_domain'] }})</span>@endif
                        </h4>
                        @if (filled($item->data['role'] ?? null)) <span>{{ $item->data['role'] }}</span> @endif
                    </div>
                    @if (filled($item->data['tech_stack'] ?? null))
                        <p class="template-two-tech">{{ $item->data['tech_stack'] }}</p>
                    @endif
                    @if (filled($item->data['url'] ?? null))
                        <a href="{{ $item->data['url'] }}" target="_blank" rel="noopener">{{ preg_replace('#^https?://(www\.)?#', '', $item->data['url']) }}</a>
                    @endif
                    @if (filled($item->data['description'] ?? null))
                        <p>{{ $item->data['description'] }}</p>
                    @endif
                </article>
            @endforeach
        @else
            @foreach ($items as $item)
                <article class="template-two-custom">
                    @if (filled($item->data['title'] ?? $item->data['name'] ?? null))
                        <h4>@include('resumes.partials.custom-section-item-title', ['item' => $item])</h4>
                    @endif
                    @if ($section->type === 'custom')
                        @include('resumes.partials.custom-section-item-content', ['item' => $item])
                    @elseif (filled($item->data['content'] ?? $item->data['description'] ?? null))
                        <p>{{ $item->data['content'] ?? $item->data['description'] }}</p>
                    @endif
                </article>
            @endforeach
        @endif
    </section>
@endforeach
