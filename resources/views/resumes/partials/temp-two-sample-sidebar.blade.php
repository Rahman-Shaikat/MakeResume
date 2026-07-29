<section class="temp-two-section temp-two-side-section">
    @include('resumes.partials.temp-two-heading', ['type' => 'skills', 'title' => 'Skills'])
    <div class="temp-two-inline-list">
        @foreach ($data['skills'] as $skill)
            <span>{{ $skill }}</span>
        @endforeach
    </div>
</section>

<section class="temp-two-section temp-two-side-section">
    @include('resumes.partials.temp-two-heading', ['type' => 'education', 'title' => 'Education'])
    @foreach ($data['education'] as $education)
        <article class="temp-two-education">
            <h4>{{ $education['school'] }}</h4>
            <p>{{ $education['degree'] }}</p>
            <div class="temp-two-side-meta">
                <span>{{ $education['location'] }}</span>
                <time>{{ $education['year'] }}</time>
            </div>
        </article>
    @endforeach
</section>

<section class="temp-two-section temp-two-side-section">
    @include('resumes.partials.temp-two-heading', ['type' => 'awards', 'title' => 'Key Achievements'])
    <div class="temp-two-feature-list">
        @foreach ($data['awards'] as $award)
            <article>
                <h4>{{ $award['title'] }}</h4>
                <p>{{ $award['description'] }}</p>
            </article>
        @endforeach
    </div>
</section>

<section class="temp-two-section temp-two-side-section">
    @include('resumes.partials.temp-two-heading', ['type' => 'custom', 'title' => 'Interests'])
    <div class="temp-two-feature-list temp-two-interests">
        <article>
            <h4>{{ $data['interest']['title'] }}</h4>
            <p>{{ $data['interest']['description'] }}</p>
        </article>
    </div>
</section>
