<section class="template-two-section template-two-main-section">
    <h3>Work Experience</h3>
    @foreach ($data['experience'] as $experience)
        <article class="template-two-experience">
            <p class="template-two-employer"><strong>{{ $experience['company'] }}</strong>, <span>{{ $experience['location'] }}</span></p>
            <p class="template-two-company-note">{{ $experience['intro'] }}</p>
            <div class="template-two-role-row">
                <h4>{{ $experience['role'] }}</h4>
                <time>{{ $experience['dates'] }}</time>
            </div>
            <ul class="template-two-achievements">
                @foreach ($experience['highlights'] as $highlight)
                    <li>{{ $highlight }}</li>
                @endforeach
            </ul>
        </article>
    @endforeach
</section>

<section class="template-two-section template-two-main-section">
    <h3>Projects</h3>
    @foreach (array_slice($data['projects'], 0, 3) as $project)
        <article class="template-two-project">
            <div class="template-two-project-heading"><h4>{{ $project['name'] }}</h4></div>
            <a href="{{ $project['url'] }}">{{ preg_replace('#^https?://(www\.)?#', '', $project['url']) }}</a>
            <p>{{ $project['description'] }}</p>
        </article>
    @endforeach
</section>
