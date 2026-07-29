@php
    $sidebarTypes = ['skills', 'education', 'courses', 'languages'];
@endphp

@foreach ($sections->where('is_visible', true) as $section)
    @continue(! in_array($section->type, $sidebarTypes, true))
    @continue($section->items->isEmpty())

    <section class="template-five-section template-five-side-section template-five-section-{{ $section->type }}">
        <h3>{{ $section->title }}</h3>

        @if (in_array($section->type, ['skills', 'languages'], true))
            <div class="template-five-skill-list">
                @foreach ($section->items as $item)
                    @php
                        $level = $item->data['level'] ?? $item->data['proficiency'] ?? null;
                        $percentage = $skillPercentage($level, $loop->index);
                    @endphp
                    <article class="template-five-skill">
                        <div class="template-five-skill-label">
                            <strong>{{ $item->data['name'] ?? '' }}</strong>
                            @if ($level)
                                <small>{{ $level }}</small>
                            @endif
                        </div>
                        <div class="template-five-skill-bar">
                            <i style="--skill-level: {{ $percentage }}%"></i>
                        </div>
                    </article>
                @endforeach
            </div>
        @elseif ($section->type === 'education')
            @foreach ($section->items as $item)
                <article class="template-five-education">
                    <h4>{{ $item->data['degree'] ?? '' }}</h4>
                    <strong>{{ $item->data['institution'] ?? '' }}</strong>
                    @if (filled($item->data['start_date'] ?? null) || filled($item->data['end_date'] ?? null))
                        <time>{{ $formatMonth($item->data['start_date'] ?? null) }} - {{ $formatMonth($item->data['end_date'] ?? null) }}</time>
                    @endif
                    @if (filled($item->data['location'] ?? null))
                        <p>{{ $item->data['location'] }}</p>
                    @endif
                    @if (filled($item->data['description'] ?? null))
                        <p>{{ $item->data['description'] }}</p>
                    @endif
                </article>
            @endforeach
        @else
            @foreach ($section->items as $item)
                <article class="template-five-education">
                    <h4>{{ $item->data['name'] ?? '' }}</h4>
                    @if (filled($item->data['provider'] ?? null))
                        <strong>{{ $item->data['provider'] }}</strong>
                    @endif
                    @if (filled($item->data['date'] ?? null))
                        <time>{{ $item->data['date'] }}</time>
                    @endif
                    @if (filled($item->data['description'] ?? null))
                        <p>{{ $item->data['description'] }}</p>
                    @endif
                </article>
            @endforeach
        @endif
    </section>
@endforeach
