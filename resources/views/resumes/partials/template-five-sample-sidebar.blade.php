<section class="template-five-section template-five-side-section">
    <h3>Skills</h3>
    <div class="template-five-skill-list">
        @foreach ($data['skills'] as $skill)
            <article class="template-five-skill">
                <div class="template-five-skill-label"><strong>{{ $skill }}</strong></div>
                <div class="template-five-skill-bar">
                    <i style="--skill-level: {{ $skillPercentage(null, $loop->index) }}%"></i>
                </div>
            </article>
        @endforeach
    </div>
</section>

<section class="template-five-section template-five-side-section">
    <h3>Education</h3>
    @foreach ($data['education'] as $education)
        <article class="template-five-education">
            <h4>{{ $education['degree'] }}</h4>
            <strong>{{ $education['school'] }}</strong>
            <time>{{ $education['year'] }}</time>
            @if ($education['location'])
                <p>{{ $education['location'] }}</p>
            @endif
        </article>
    @endforeach
</section>
