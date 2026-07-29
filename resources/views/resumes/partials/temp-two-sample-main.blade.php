<section class="temp-two-section temp-two-main-section">
    @include('resumes.partials.temp-two-heading', ['type' => 'summary', 'title' => 'Summary'])
    <p class="temp-two-summary">{{ $content['summary'] }}</p>
</section>

<section class="temp-two-section temp-two-main-section">
    @include('resumes.partials.temp-two-heading', ['type' => 'experience', 'title' => 'Experience'])
    @foreach ($data['experience'] as $experience)
        <article class="temp-two-experience">
            <div class="temp-two-entry-heading">
                <strong>{{ $experience['company'] }}</strong>
                <span>{{ $experience['location'] }}</span>
            </div>
            <div class="temp-two-entry-heading temp-two-role-row">
                <h4>{{ $experience['role'] }}</h4>
                <time>{{ $experience['dates'] }}</time>
            </div>
            <ul class="temp-two-bullets">
                @foreach ($experience['highlights'] as $highlight)
                    <li>{{ $highlight }}</li>
                @endforeach
            </ul>
        </article>
    @endforeach
</section>
