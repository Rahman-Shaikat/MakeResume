<section class="template-six-section template-six-main-section">
    <h3>Summary</h3>
    <p class="template-six-summary">{{ $content['summary'] }}</p>
</section>

<section class="template-six-section template-six-main-section">
    <h3>Experience</h3>
    @foreach ($data['experience'] as $experience)
        <article class="template-six-experience">
            <div class="template-six-entry-row">
                <div>
                    <h4>{{ $experience['role'] }}</h4>
                    <strong>{{ $experience['company'] }}</strong>
                </div>
                <time>{{ $experience['dates'] }}</time>
            </div>
        </article>
    @endforeach
</section>

<section class="template-six-section template-six-main-section">
    <h3>Projects</h3>
    @foreach ($data['projects'] as $project)
        <article class="template-six-project">
            <h4>{{ $project['name'] }}</h4>
            @if (filled($project['role'] ?? null))
                <strong>{{ $project['role'] }}</strong>
            @endif
            @if (filled($project['description'] ?? null))
                <p>{{ $project['description'] }}</p>
            @endif
            @if (filled($project['tech_stack'] ?? null))
                <p class="template-six-project-tech">{{ $project['tech_stack'] }}</p>
            @endif
        </article>
    @endforeach
</section>
