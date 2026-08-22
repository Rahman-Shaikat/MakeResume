@php
    $excludedTypes = ['personal', 'skills', 'languages', 'education', 'courses'];
@endphp

@foreach ($sections->where('is_visible', true) as $section)
    @continue(in_array($section->type, $excludedTypes, true))

    @php
        $items = $section->items;
        $hasContent = $section->type === 'summary'
            ? filled($content['summary'] ?? null)
            : $items->isNotEmpty();
    @endphp

    @continue(! $hasContent)

    <section class="template-six-section template-six-main-section template-six-section-{{ $section->type }}">
        <h3>{{ $section->title }}</h3>

        @if ($section->type === 'summary')
            <p class="template-six-summary">{{ $content['summary'] }}</p>
        @elseif ($section->type === 'experience')
            @foreach ($items as $item)
                <article class="template-six-experience">
                    <div class="template-six-entry-row">
                        <div>
                            <h4>{{ $item->data['title'] ?? '' }}</h4>
                            <strong>
                                @if (filled($item->data['company_website'] ?? null))
                                    <a class="resume-company-link" href="{{ $item->data['company_website'] }}" target="_blank" rel="noopener">{{ $item->data['company'] ?? '' }}</a>
                                @else
                                    {{ $item->data['company'] ?? '' }}
                                @endif
                            </strong>
                        </div>
                        @if (filled($item->data['start_date'] ?? null) || filled($item->data['end_date'] ?? null))
                            <time>
                                {{ $formatMonth($item->data['start_date'] ?? null) }}
                                @if (filled($item->data['start_date'] ?? null)) - @endif
                                {{ ($item->data['current'] ?? false) ? 'Present' : $formatMonth($item->data['end_date'] ?? null) }}
                            </time>
                        @endif
                    </div>
                    @if (filled($item->data['location'] ?? null))
                        <span class="template-six-location">{{ $item->data['location'] }}</span>
                    @endif
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
        @elseif ($section->type === 'projects')
            @foreach ($items as $item)
                <article @class(['template-six-project', 'project-without-description' => ! filled($item->data['description'] ?? null)])>
                    <div class="template-six-entry-row">
                        <div>
                            <h4>
                                {{ $item->data['name'] ?? '' }}@if (filled($item->data['project_domain'] ?? null)) <span class="resume-project-domain">({{ $item->data['project_domain'] }})</span>@endif
                            </h4>
                            @if (filled($item->data['role'] ?? null))
                                <strong>{{ $item->data['role'] }}</strong>
                            @endif
                        </div>
                        @if (filled($item->data['url'] ?? null))
                            <a href="{{ $item->data['url'] }}" target="_blank" rel="noopener">{{ preg_replace('#^https?://(www\.)?#', '', $item->data['url']) }}</a>
                        @endif
                    </div>
                    @if (filled($item->data['tech_stack'] ?? null))
                        <p class="template-six-project-tech">{{ $item->data['tech_stack'] }}</p>
                    @endif
                    @if (filled($item->data['description'] ?? null))
                        <p>{!! nl2br(e($item->data['description'])) !!}</p>
                    @endif
                </article>
            @endforeach
        @else
            @foreach ($items as $item)
                <article class="template-six-project">
                    <h4>{{ $item->data['title'] ?? $item->data['name'] ?? '' }}</h4>
                    @if (filled($item->data['organization'] ?? null))
                        <strong>{{ $item->data['organization'] }}</strong>
                    @endif
                    @if (filled($item->data['date'] ?? null))
                        <time>{{ $item->data['date'] }}</time>
                    @endif
                    @if (filled($item->data['content'] ?? $item->data['description'] ?? null))
                        <p>{{ $item->data['content'] ?? $item->data['description'] }}</p>
                    @endif
                </article>
            @endforeach
        @endif
    </section>
@endforeach
