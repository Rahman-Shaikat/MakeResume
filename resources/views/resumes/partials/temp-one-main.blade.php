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

    <section class="temp-one-section temp-one-main-section temp-one-section-{{ $section->type }}">
        <h3>{{ $section->title }}</h3>

        @if ($section->type === 'summary')
            <p class="temp-one-summary">{{ $content['summary'] }}</p>
        @elseif ($section->type === 'experience')
            @foreach ($items as $item)
                <article class="temp-one-experience">
                    <p class="temp-one-employer">
                        <strong>{{ $item->data['company'] ?? '' }}</strong>
                        @if (filled($item->data['location'] ?? null))
                            <span>, {{ $item->data['location'] }}</span>
                        @endif
                    </p>
                    <div class="temp-one-role-row">
                        <h4>{{ $item->data['title'] ?? '' }}</h4>
                        @if (filled($item->data['start_date'] ?? null) || filled($item->data['end_date'] ?? null))
                            <time>
                                {{ $formatMonth($item->data['start_date'] ?? null) }}
                                @if (filled($item->data['start_date'] ?? null)) - @endif
                                {{ ($item->data['current'] ?? false) ? 'Present' : $formatMonth($item->data['end_date'] ?? null) }}
                            </time>
                        @endif
                    </div>
                    @if (filled($item->data['description'] ?? null))
                        <ul class="temp-one-achievements">
                            @foreach (preg_split('/\r\n|\r|\n/', $item->data['description']) as $achievement)
                                @if (filled($achievement)) <li>{{ $achievement }}</li> @endif
                            @endforeach
                        </ul>
                    @endif
                </article>
            @endforeach
        @elseif ($section->type === 'projects')
            @foreach ($items as $item)
                <article class="temp-one-project">
                    <div class="temp-one-project-heading">
                        <h4>{{ $item->data['name'] ?? '' }}</h4>
                        @if (filled($item->data['role'] ?? null)) <span>{{ $item->data['role'] }}</span> @endif
                    </div>
                    @if (filled($item->data['tech_stack'] ?? null))
                        <p class="temp-one-tech">{{ $item->data['tech_stack'] }}</p>
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
                <article class="temp-one-custom">
                    @if (filled($item->data['title'] ?? $item->data['name'] ?? null))
                        <h4>{{ $item->data['title'] ?? $item->data['name'] }}</h4>
                    @endif
                    @if (filled($item->data['content'] ?? $item->data['description'] ?? null))
                        <p>{{ $item->data['content'] ?? $item->data['description'] }}</p>
                    @endif
                </article>
            @endforeach
        @endif
    </section>
@endforeach
