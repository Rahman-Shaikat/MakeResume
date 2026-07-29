@php
    $sidebarTypes = ['skills', 'education', 'courses', 'awards', 'languages'];
@endphp

@foreach ($sections->where('is_visible', true) as $section)
    @continue(! in_array($section->type, $sidebarTypes, true))
    @continue($section->items->isEmpty())

    <section class="temp-one-section temp-one-side-section temp-one-section-{{ $section->type }}">
        <h3>{{ $section->title }}</h3>

        @if ($section->type === 'skills')
            @foreach ($section->items->groupBy(fn ($item) => $item->data['category'] ?? 'Skills') as $category => $skills)
                @if ($category !== 'Skills') <h4 class="temp-one-side-label">{{ $category }}:</h4> @endif
                <ul>
                    @foreach ($skills as $item)
                        <li>
                            {{ $item->data['name'] ?? '' }}
                            @if (filled($item->data['level'] ?? null)) <small>({{ $item->data['level'] }})</small> @endif
                        </li>
                    @endforeach
                </ul>
            @endforeach
        @elseif ($section->type === 'education')
            @foreach ($section->items as $item)
                <article class="temp-one-side-entry">
                    <h4>{{ $item->data['institution'] ?? '' }}</h4>
                    <p>{{ $item->data['degree'] ?? '' }}</p>
                    @if (filled($item->data['location'] ?? null)) <p>{{ $item->data['location'] }}</p> @endif
                    @if (filled($item->data['start_date'] ?? null) || filled($item->data['end_date'] ?? null))
                        <time>{{ $formatMonth($item->data['start_date'] ?? null) }} - {{ $formatMonth($item->data['end_date'] ?? null) }}</time>
                    @endif
                    @if (filled($item->data['description'] ?? null)) <p>{{ $item->data['description'] }}</p> @endif
                </article>
            @endforeach
        @elseif ($section->type === 'languages')
            <ul>
                @foreach ($section->items as $item)
                    <li>
                        {{ $item->data['name'] ?? '' }}
                        @if (filled($item->data['proficiency'] ?? null)) ({{ $item->data['proficiency'] }}) @endif
                    </li>
                @endforeach
            </ul>
        @else
            <ul>
                @foreach ($section->items as $item)
                    <li>
                        <strong>{{ $item->data['title'] ?? $item->data['name'] ?? '' }}</strong>
                        @if (filled($item->data['organization'] ?? $item->data['provider'] ?? null))
                            <span>{{ $item->data['organization'] ?? $item->data['provider'] }}</span>
                        @endif
                        @if (filled($item->data['date'] ?? null)) <small>{{ $item->data['date'] }}</small> @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </section>
@endforeach
