<section class="template-three-section template-three-main-section">
    @include('resumes.partials.template-three-heading', ['type' => 'summary', 'title' => 'Summary'])
    <p class="template-three-summary">{{ $content['summary'] }}</p>
</section>

<section class="template-three-section template-three-main-section">
    @include('resumes.partials.template-three-heading', ['type' => 'experience', 'title' => 'Experience'])
    @foreach ($data['experience'] as $experience)
        <article class="template-three-experience">
            <div class="template-three-entry-heading">
                <strong>{{ $experience['company'] }}</strong>
                <span>{{ $experience['location'] }}</span>
            </div>
            <div class="template-three-entry-heading template-three-role-row">
                <h4>{{ $experience['role'] }}</h4>
                <time>{{ $experience['dates'] }}</time>
            </div>
            <ul class="template-three-bullets">
                @foreach ($experience['highlights'] as $highlight)
                    <li>{{ $highlight }}</li>
                @endforeach
            </ul>
        </article>
    @endforeach
</section>
