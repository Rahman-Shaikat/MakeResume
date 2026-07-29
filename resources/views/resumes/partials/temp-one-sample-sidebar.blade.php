<section class="temp-one-section temp-one-side-section">
    <h3>Skills</h3>
    <h4 class="temp-one-side-label">Technical Skills:</h4>
    <ul>
        @foreach ($data['skills'] as $skill)
            <li>{{ $skill }}</li>
        @endforeach
    </ul>
</section>

<section class="temp-one-section temp-one-side-section">
    <h3>Languages</h3>
    <ul>
        @foreach ($data['languages'] as $language)
            <li>{{ $language['name'] }} ({{ $language['level'] }})</li>
        @endforeach
    </ul>
</section>

<section class="temp-one-section temp-one-side-section">
    <h3>Education</h3>
    @foreach (array_slice($data['education'], 0, 2) as $education)
        <article class="temp-one-side-entry">
            <h4>{{ $education['school'] }}</h4>
            <p>{{ $education['degree'] }}</p>
            <p>{{ $education['location'] }}</p>
            <time>{{ $education['year'] }}</time>
        </article>
    @endforeach
</section>

<section class="temp-one-section temp-one-side-section">
    <h3>Other</h3>
    <ul>
        <li><strong>{{ $data['course']['name'] }}</strong><span>{{ $data['course']['provider'] }}</span></li>
    </ul>
</section>
