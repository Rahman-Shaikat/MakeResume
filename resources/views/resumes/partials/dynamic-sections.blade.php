<div class="resume-dynamic-sections">
    @foreach ($sections->where('is_visible', true) as $section)
        @continue($section->type === 'personal')

        @php
            $items = $section->items;
            $hasContent = $section->type === 'summary'
                ? filled($content['summary'] ?? null)
                : $items->isNotEmpty();
        @endphp

        @continue(! $hasContent)

        <section class="resume-section resume-dynamic-section resume-section-{{ $section->type }}">
            <h3>{{ $section->title }}</h3>

            @if ($section->type === 'summary')
                <p class="resume-preserve-lines">{{ $content['summary'] }}</p>
            @elseif ($section->type === 'skills')
                <div class="resume-skills">
                    @foreach ($items as $item)
                        <span>
                            {{ $item->data['name'] ?? '' }}
                            @if (filled($item->data['category'] ?? null) || filled($item->data['level'] ?? null))
                                <small>
                                    {{ $item->data['category'] ?? '' }}
                                    @if (filled($item->data['category'] ?? null) && filled($item->data['level'] ?? null)) / @endif
                                    {{ $item->data['level'] ?? '' }}
                                </small>
                            @endif
                        </span>
                    @endforeach
                </div>
            @elseif ($section->type === 'experience')
                @foreach ($items as $item)
                    <article class="experience-item">
                        <h4>{{ $item->data['title'] ?? '' }}</h4>
                        @if (filled($item->data['company'] ?? null))
                            <h5>
                                @if (filled($item->data['company_website'] ?? null))
                                    <a class="resume-company-link" href="{{ $item->data['company_website'] }}" target="_blank" rel="noopener">{{ $item->data['company'] }}</a>
                                @else
                                    {{ $item->data['company'] }}
                                @endif
                            </h5>
                        @endif
                        <div class="resume-meta">
                            @if (filled($item->data['start_date'] ?? null) || filled($item->data['end_date'] ?? null))
                                <span>
                                    {{ $item->data['start_date'] ?? '' }}
                                    @if (filled($item->data['start_date'] ?? null)) - @endif
                                    {{ ($item->data['current'] ?? false) ? 'Present' : ($item->data['end_date'] ?? '') }}
                                </span>
                            @endif
                            @if (filled($item->data['location'] ?? null))
                                <span>{{ $item->data['location'] }}</span>
                            @endif
                        </div>
                        @if (filled($item->data['description'] ?? null))
                            <p class="resume-preserve-lines">{{ $item->data['description'] }}</p>
                        @endif
                    </article>
                @endforeach
            @elseif ($section->type === 'education')
                @foreach ($items as $item)
                    <article class="education-item">
                        <h4>{{ $item->data['degree'] ?? '' }}</h4>
                        @if (filled($item->data['institution'] ?? null))
                            <h5>{{ $item->data['institution'] }}</h5>
                        @endif
                        <div class="resume-meta">
                            @if (filled($item->data['start_date'] ?? null) || filled($item->data['end_date'] ?? null))
                                <span>{{ $item->data['start_date'] ?? '' }} - {{ $item->data['end_date'] ?? '' }}</span>
                            @endif
                            @if (filled($item->data['location'] ?? null))
                                <span>{{ $item->data['location'] }}</span>
                            @endif
                        </div>
                        @if (filled($item->data['description'] ?? null))
                            <p class="resume-preserve-lines">{{ $item->data['description'] }}</p>
                        @endif
                    </article>
                @endforeach
            @elseif ($section->type === 'projects')
                @foreach ($items as $item)
                    <article @class(['project-item', 'project-without-description' => ! filled($item->data['description'] ?? null)])>
                        <h4>
                            {{ $item->data['name'] ?? '' }}@if (filled($item->data['project_domain'] ?? null)) <span class="resume-project-domain">({{ $item->data['project_domain'] }})</span>@endif
                        </h4>
                        @if (filled($item->data['role'] ?? null))
                            <h5>{{ $item->data['role'] }}</h5>
                        @endif
                        @if (filled($item->data['url'] ?? null))
                            <a href="{{ $item->data['url'] }}" target="_blank" rel="noopener">{{ preg_replace('#^https?://(www\.)?#', '', $item->data['url']) }}</a>
                        @endif
                        @if (filled($item->data['tech_stack'] ?? null))
                            <div class="resume-tech-stack">{{ $item->data['tech_stack'] }}</div>
                        @endif
                        @if (filled($item->data['description'] ?? null))
                            <p class="resume-preserve-lines">{{ $item->data['description'] }}</p>
                        @endif
                    </article>
                @endforeach
            @elseif ($section->type === 'courses')
                @foreach ($items as $item)
                    <article class="course-item">
                        <h4>{{ $item->data['name'] ?? '' }}</h4>
                        @if (filled($item->data['provider'] ?? null))
                            <h5>{{ $item->data['provider'] }}</h5>
                        @endif
                        @if (filled($item->data['date'] ?? null))
                            <div class="resume-meta"><span>{{ $item->data['date'] }}</span></div>
                        @endif
                        @if (filled($item->data['description'] ?? null))
                            <p class="resume-preserve-lines">{{ $item->data['description'] }}</p>
                        @endif
                    </article>
                @endforeach
            @elseif ($section->type === 'awards')
                @foreach ($items as $item)
                    <article class="award-item">
                        <h4>{{ $item->data['title'] ?? '' }}</h4>
                        @if (filled($item->data['organization'] ?? null))
                            <h5>{{ $item->data['organization'] }}</h5>
                        @endif
                        @if (filled($item->data['date'] ?? null))
                            <div class="resume-meta"><span>{{ $item->data['date'] }}</span></div>
                        @endif
                        @if (filled($item->data['description'] ?? null))
                            <p class="resume-preserve-lines">{{ $item->data['description'] }}</p>
                        @endif
                    </article>
                @endforeach
            @elseif ($section->type === 'languages')
                @foreach ($items as $item)
                    <div class="dynamic-language-item">
                        <strong>{{ $item->data['name'] ?? '' }}</strong>
                        @if (filled($item->data['proficiency'] ?? null))
                            <span>{{ $item->data['proficiency'] }}</span>
                        @endif
                    </div>
                @endforeach
            @else
                @foreach ($items as $item)
                    <article class="custom-resume-item">
                        @if (filled($item->data['title'] ?? null))
                            <h4>{{ $item->data['title'] }}</h4>
                        @endif
                        @if (filled($item->data['content'] ?? null))
                            <p class="resume-preserve-lines">{{ $item->data['content'] }}</p>
                        @endif
                    </article>
                @endforeach
            @endif
        </section>
    @endforeach
</div>
