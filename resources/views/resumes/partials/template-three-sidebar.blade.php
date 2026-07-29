@php
    $excludedTypes = ['personal', 'summary', 'experience', 'projects'];
@endphp

@foreach ($sections->where('is_visible', true) as $section)
    @continue(in_array($section->type, $excludedTypes, true))
    @continue($section->items->isEmpty())

    <section class="template-three-section template-three-side-section template-three-section-{{ $section->type }}">
        @include('resumes.partials.template-three-heading', ['type' => $section->type, 'title' => $section->title])

        @if ($section->type === 'skills')
            <div class="template-three-inline-list">
                @foreach ($section->items as $item)
                    <span>
                        {{ $item->data['name'] ?? '' }}
                        @if (filled($item->data['level'] ?? null))
                            <small>({{ $item->data['level'] }})</small>
                        @endif
                    </span>
                @endforeach
            </div>
        @elseif ($section->type === 'education')
            @foreach ($section->items as $item)
                <article class="template-three-education">
                    <h4>{{ $item->data['institution'] ?? '' }}</h4>
                    <p>{{ $item->data['degree'] ?? '' }}</p>
                    <div class="template-three-side-meta">
                        <span>{{ $item->data['location'] ?? '' }}</span>
                        @if (filled($item->data['start_date'] ?? null) || filled($item->data['end_date'] ?? null))
                            <time>{{ $formatMonth($item->data['start_date'] ?? null) }} - {{ $formatMonth($item->data['end_date'] ?? null) }}</time>
                        @endif
                    </div>
                    @if (filled($item->data['description'] ?? null))
                        <p>{{ $item->data['description'] }}</p>
                    @endif
                </article>
            @endforeach
        @elseif ($section->type === 'languages')
            <div class="template-three-inline-list">
                @foreach ($section->items as $item)
                    <span>
                        {{ $item->data['name'] ?? '' }}
                        @if (filled($item->data['proficiency'] ?? null))
                            <small>({{ $item->data['proficiency'] }})</small>
                        @endif
                    </span>
                @endforeach
            </div>
        @else
            <div class="template-three-feature-list">
                @foreach ($section->items as $item)
                    <article>
                        <h4>{{ $item->data['title'] ?? $item->data['name'] ?? '' }}</h4>
                        @if (filled($item->data['organization'] ?? $item->data['provider'] ?? null))
                            <strong>{{ $item->data['organization'] ?? $item->data['provider'] }}</strong>
                        @endif
                        @if (filled($item->data['date'] ?? null))
                            <small>{{ $item->data['date'] }}</small>
                        @endif
                        @if (filled($item->data['content'] ?? $item->data['description'] ?? null))
                            <p>{{ $item->data['content'] ?? $item->data['description'] }}</p>
                        @endif
                    </article>
                @endforeach
            </div>
        @endif
    </section>
@endforeach
