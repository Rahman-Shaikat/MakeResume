<section class="template-three-section template-three-side-section">
    @include('resumes.partials.template-three-heading', ['type' => 'skills', 'title' => 'Skills'])
    <div class="template-three-inline-list">
        @foreach ($data['skills'] as $skill)
            <span>{{ $skill }}</span>
        @endforeach
    </div>
</section>

<section class="template-three-section template-three-side-section">
    @include('resumes.partials.template-three-heading', ['type' => 'education', 'title' => 'Education'])
    @foreach ($data['education'] as $education)
        <article class="template-three-education">
            <h4>{{ $education['school'] }}</h4>
            <p>{{ $education['degree'] }}</p>
            <div class="template-three-side-meta">
                <span>{{ $education['location'] }}</span>
                <time>{{ $education['year'] }}</time>
            </div>
        </article>
    @endforeach
</section>

<section class="template-three-section template-three-side-section">
    @include('resumes.partials.template-three-heading', ['type' => 'awards', 'title' => 'Key Achievements'])
    <div class="template-three-feature-list">
        @foreach ($data['awards'] as $award)
            <article>
                <h4>{{ $award['title'] }}</h4>
                <p>{{ $award['description'] }}</p>
            </article>
        @endforeach
    </div>
</section>

<section class="template-three-section template-three-side-section">
    @include('resumes.partials.template-three-heading', ['type' => 'custom', 'title' => 'Interests'])
    <div class="template-three-feature-list template-three-interests">
        <article>
            <h4>{{ $data['interest']['title'] }}</h4>
            <p>{{ $data['interest']['description'] }}</p>
        </article>
    </div>
</section>
