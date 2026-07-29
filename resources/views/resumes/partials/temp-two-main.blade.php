@php
    $mainTypes = ['summary', 'experience', 'projects'];
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

    <section class="temp-two-section temp-two-main-section temp-two-section-{{ $section->type }}">
        @include('resumes.partials.temp-two-heading', ['type' => $section->type, 'title' => $section->title])

        @if ($section->type === 'summary')
            <p class="temp-two-summary">{{ $content['summary'] }}</p>
        @elseif ($section->type === 'experience')
            @foreach ($items as $item)
                <article class="temp-two-experience">
                    <div class="temp-two-entry-heading">
                        <strong>{{ $item->data['company'] ?? '' }}</strong>
                        @if (filled($item->data['location'] ?? null))
                            <span>{{ $item->data['location'] }}</span>
                        @endif
                    </div>
                    <div class="temp-two-entry-heading temp-two-role-row">
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
                        <ul class="temp-two-bullets">
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
                <article class="temp-two-project">
                    <div class="temp-two-entry-heading">
                        <strong>{{ $item->data['name'] ?? '' }}</strong>
                        @if (filled($item->data['role'] ?? null))
                            <span>{{ $item->data['role'] }}</span>
                        @endif
                    </div>
                    @if (filled($item->data['tech_stack'] ?? null))
                        <p class="temp-two-detail">{{ $item->data['tech_stack'] }}</p>
                    @endif
                    @if (filled($item->data['url'] ?? null))
                        <a href="{{ $item->data['url'] }}" target="_blank" rel="noopener">{{ preg_replace('#^https?://(www\.)?#', '', $item->data['url']) }}</a>
                    @endif
                    @if (filled($item->data['description'] ?? null))
                        <p>{{ $item->data['description'] }}</p>
                    @endif
                </article>
            @endforeach
        @endif
    </section>
@endforeach
