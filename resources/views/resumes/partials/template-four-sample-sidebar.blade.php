<section class="template-four-section template-four-side-section">
    <h3>Projects</h3>
    @foreach ($data['projects'] as $project)
        <article class="template-four-project">
            <h4>{{ $project['name'] }}</h4>
            <p>{{ $project['description'] }}</p>
            <a href="{{ $project['url'] }}" target="_blank" rel="noopener">{{ preg_replace('#^https?://(www\.)?#', '', $project['url']) }}</a>
        </article>
    @endforeach
</section>

<section class="template-four-section template-four-side-section">
    <h3>Key Achievements</h3>
    <div class="template-four-feature-list">
        @foreach ($data['awards'] as $award)
            <article>
                <span class="template-four-feature-icon" aria-hidden="true">◆</span>
                <div>
                    <h4>{{ $award['title'] }}</h4>
                    <p>{{ $award['description'] }}</p>
                </div>
            </article>
        @endforeach
    </div>
</section>

<section class="template-four-section template-four-side-section">
    <h3>Skills</h3>
    <p class="template-four-inline-list">
        @foreach ($data['skills'] as $skill)
            <span>{{ $skill }}</span>
        @endforeach
    </p>
</section>

<section class="template-four-section template-four-side-section">
    <h3>Courses</h3>
    <div class="template-four-feature-list template-four-course-list">
        <article>
            <div>
                <h4>{{ $data['course']['name'] }}</h4>
                <strong>{{ $data['course']['provider'] }}</strong>
                <p>{{ $data['course']['description'] }}</p>
            </div>
        </article>
    </div>
</section>
