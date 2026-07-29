@php
    $excludedTypes = ['personal', 'skills', 'education', 'courses', 'languages'];
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

    <section class="template-five-section template-five-main-section template-five-section-{{ $section->type }}">
        <h3>{{ $section->title }}</h3>

        @if ($section->type === 'summary')
            <p class="template-five-summary">{{ $content['summary'] }}</p>
        @elseif ($section->type === 'experience')
            @foreach ($items as $item)
                <article class="template-five-experience">
                    <h4>{{ $item->data['title'] ?? '' }}</h4>
                    <div class="template-five-entry-meta">
                        <strong>{{ $item->data['company'] ?? '' }}</strong>
                        @if (filled($item->data['location'] ?? null))
                            <span>- {{ $item->data['location'] }}</span>
                        @endif
                        @if (filled($item->data['start_date'] ?? null) || filled($item->data['end_date'] ?? null))
                            <time>
                                {{ $formatMonth($item->data['start_date'] ?? null) }}
                                @if (filled($item->data['start_date'] ?? null)) - @endif
                                {{ ($item->data['current'] ?? false) ? 'Present' : $formatMonth($item->data['end_date'] ?? null) }}
                            </time>
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
        @elseif ($section->type === 'projects')
            @foreach ($items as $item)
                <article class="template-five-project">
                    <h4>{{ $item->data['name'] ?? '' }}</h4>
                    <div class="template-five-entry-meta">
                        @if (filled($item->data['role'] ?? null))
                            <strong>{{ $item->data['role'] }}</strong>
                        @endif
                        @if (filled($item->data['url'] ?? null))
                            <a href="{{ $item->data['url'] }}" target="_blank" rel="noopener">{{ preg_replace('#^https?://(www\.)?#', '', $item->data['url']) }}</a>
                        @endif
                    </div>
                    @if (filled($item->data['tech_stack'] ?? null))
                        <p><b>Skills Used:</b> {{ $item->data['tech_stack'] }}</p>
                    @endif
                    @if (filled($item->data['description'] ?? null))
                        <p>{{ $item->data['description'] }}</p>
                    @endif
                </article>
            @endforeach
        @else
            @foreach ($items as $item)
                <article class="template-five-project">
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
