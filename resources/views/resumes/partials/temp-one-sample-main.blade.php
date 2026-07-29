<section class="temp-one-section temp-one-main-section">
    <h3>Work Experience</h3>
    @foreach ($data['experience'] as $experience)
        <article class="temp-one-experience">
            <p class="temp-one-employer"><strong>{{ $experience['company'] }}</strong>, <span>{{ $experience['location'] }}</span></p>
            <p class="temp-one-company-note">{{ $experience['intro'] }}</p>
            <div class="temp-one-role-row">
                <h4>{{ $experience['role'] }}</h4>
                <time>{{ $experience['dates'] }}</time>
            </div>
            <ul class="temp-one-achievements">
                @foreach ($experience['highlights'] as $highlight)
                    <li>{{ $highlight }}</li>
                @endforeach
            </ul>
        </article>
    @endforeach
</section>

<section class="temp-one-section temp-one-main-section">
    <h3>Projects</h3>
    @foreach (array_slice($data['projects'], 0, 3) as $project)
        <article class="temp-one-project">
            <div class="temp-one-project-heading"><h4>{{ $project['name'] }}</h4></div>
            <a href="{{ $project['url'] }}">{{ preg_replace('#^https?://(www\.)?#', '', $project['url']) }}</a>
            <p>{{ $project['description'] }}</p>
        </article>
    @endforeach
</section>
