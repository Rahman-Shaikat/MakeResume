<section class="template-four-section template-four-main-section">
    <h3>Summary</h3>
    <p class="template-four-summary">{{ $content['summary'] }}</p>
</section>

<section class="template-four-section template-four-main-section">
    <h3>Experience</h3>
    @foreach ($data['experience'] as $experience)
        <article class="template-four-experience">
            <div class="template-four-entry-row">
                <h4>{{ $experience['role'] }}</h4>
                <time>{{ $experience['dates'] }}</time>
            </div>
            <div class="template-four-entry-row template-four-organization-row">
                <strong>{{ $experience['company'] }}</strong>
                <span>{{ $experience['location'] }}</span>
            </div>
            <ul>
                @foreach ($experience['highlights'] as $highlight)
                    <li>{{ $highlight }}</li>
                @endforeach
            </ul>
        </article>
    @endforeach
</section>

<section class="template-four-section template-four-main-section">
    <h3>Education</h3>
    @foreach ($data['education'] as $education)
        <article class="template-four-education">
            <div class="template-four-entry-row">
                <h4>{{ $education['degree'] }}</h4>
                <time>{{ $education['year'] }}</time>
            </div>
            <div class="template-four-entry-row template-four-organization-row">
                <strong>{{ $education['school'] }}</strong>
                <span>{{ $education['location'] }}</span>
            </div>
        </article>
    @endforeach
</section>
