<section class="template-five-section template-five-main-section">
    <h3>Summary</h3>
    <p class="template-five-summary">{{ $content['summary'] }}</p>
</section>

<section class="template-five-section template-five-main-section">
    <h3>Experience</h3>
    @foreach ($data['experience'] as $experience)
        <article class="template-five-experience">
            <h4>{{ $experience['role'] }}</h4>
            <div class="template-five-entry-meta">
                <strong>{{ $experience['company'] }}</strong>
                @if ($experience['location'])
                    <span>- {{ $experience['location'] }}</span>
                @endif
                <time>{{ $experience['dates'] }}</time>
            </div>
            <ul>
                @foreach ($experience['highlights'] as $highlight)
                    <li>{{ $highlight }}</li>
                @endforeach
            </ul>
        </article>
    @endforeach
</section>

<section class="template-five-section template-five-main-section">
    <h3>Projects</h3>
    @foreach ($data['projects'] as $project)
        <article class="template-five-project">
            <h4>{{ $project['name'] }}</h4>
            <div class="template-five-entry-meta">
                <strong>{{ $project['role'] }}</strong>
                <time>{{ $project['dates'] }}</time>
            </div>
            <p><b>Skills Used:</b> {{ $project['tech_stack'] }}</p>
        </article>
    @endforeach
</section>
