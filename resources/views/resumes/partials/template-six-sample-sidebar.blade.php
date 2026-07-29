<section class="template-six-section template-six-side-section">
    <h3>Skills</h3>
    <div class="template-six-skill-list">
        @foreach ($data['skills'] as $skill)
            <article class="template-six-skill">
                <div><span>{{ $skill }}</span></div>
                <div class="template-six-skill-bar">
                    <i style="--skill-level: {{ $skillPercentage(null, $loop->index) }}%"></i>
                </div>
            </article>
        @endforeach
    </div>
</section>

<section class="template-six-section template-six-side-section">
    <h3>Languages</h3>
    <div class="template-six-language-list">
        @foreach ($data['languages'] as $language)
            @php
                $dots = $languageDots($language['level'] ?? null, $loop->index);
            @endphp
            <article>
                <span>{{ $language['name'] }}</span>
                <div aria-label="{{ $language['level'] ?? $dots.' out of 5' }}">
                    @for ($dot = 1; $dot <= 5; $dot++)
                        <i class="{{ $dot <= $dots ? 'is-filled' : '' }}"></i>
                    @endfor
                </div>
            </article>
        @endforeach
    </div>
</section>

<section class="template-six-section template-six-side-section">
    <h3>Education</h3>
    @foreach ($data['education'] as $education)
        <article class="template-six-education">
            <h4>{{ $education['degree'] }}</h4>
            <time>{{ $education['year'] }}</time>
            <strong>{{ $education['school'] }}</strong>
            @if ($education['location'])
                <p>{{ $education['location'] }}</p>
            @endif
        </article>
    @endforeach
</section>
