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

    <section class="template-three-section template-three-main-section template-three-section-{{ $section->type }}">
        @include('resumes.partials.template-three-heading', ['type' => $section->type, 'title' => $section->title])

        @if ($section->type === 'summary')
            <p class="template-three-summary">{{ $content['summary'] }}</p>
        @elseif ($section->type === 'experience')
            @foreach ($items as $item)
                <article class="template-three-experience">
                    <div class="template-three-entry-heading">
                        <strong>{{ $item->data['company'] ?? '' }}</strong>
                        @if (filled($item->data['location'] ?? null))
                            <span>{{ $item->data['location'] }}</span>
                        @endif
                    </div>
                    <div class="template-three-entry-heading template-three-role-row">
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
                        <ul class="template-three-bullets">
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
                <article class="template-three-project">
                    <div class="template-three-entry-heading">
                        <strong>{{ $item->data['name'] ?? '' }}</strong>
                        @if (filled($item->data['role'] ?? null))
                            <span>{{ $item->data['role'] }}</span>
                        @endif
                    </div>
                    @if (filled($item->data['tech_stack'] ?? null))
                        <p class="template-three-detail">{{ $item->data['tech_stack'] }}</p>
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
