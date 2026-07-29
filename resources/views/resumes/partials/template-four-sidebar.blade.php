@php
    $excludedTypes = ['personal', 'summary', 'experience', 'education'];
@endphp

@foreach ($sections->where('is_visible', true) as $section)
    @continue(in_array($section->type, $excludedTypes, true))
    @continue($section->items->isEmpty())

    <section class="template-four-section template-four-side-section template-four-section-{{ $section->type }}">
        <h3>{{ $section->type === 'awards' ? 'Key Achievements' : $section->title }}</h3>

        @if ($section->type === 'projects')
            @foreach ($section->items as $item)
                <article class="template-four-project">
                    <h4>{{ $item->data['name'] ?? '' }}</h4>
                    @if (filled($item->data['role'] ?? null))
                        <strong>{{ $item->data['role'] }}</strong>
                    @endif
                    @if (filled($item->data['description'] ?? null))
                        <p>{{ $item->data['description'] }}</p>
                    @endif
                    @if (filled($item->data['tech_stack'] ?? null))
                        <p class="template-four-side-detail">{{ $item->data['tech_stack'] }}</p>
                    @endif
                    @if (filled($item->data['url'] ?? null))
                        <a href="{{ $item->data['url'] }}" target="_blank" rel="noopener">{{ preg_replace('#^https?://(www\.)?#', '', $item->data['url']) }}</a>
                    @endif
                </article>
            @endforeach
        @elseif (in_array($section->type, ['skills', 'languages'], true))
            <p class="template-four-inline-list">
                @foreach ($section->items as $item)
                    <span>
                        {{ $item->data['name'] ?? '' }}@if (filled($item->data['level'] ?? $item->data['proficiency'] ?? null)) ({{ $item->data['level'] ?? $item->data['proficiency'] }})@endif
                    </span>
                @endforeach
            </p>
        @else
            <div class="template-four-feature-list">
                @foreach ($section->items as $item)
                    <article>
                        <span class="template-four-feature-icon" aria-hidden="true">◆</span>
                        <div>
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
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>
@endforeach
