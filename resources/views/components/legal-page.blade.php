@props([
    'eyebrow',
    'title',
    'summary',
    'updatedAt',
])

<section class="legal-hero">
    <div class="container-xl legal-hero-inner">
        <span class="legal-hero-eyebrow">{{ $eyebrow }}</span>
        <h1>{{ $title }}</h1>
        <p>{{ $summary }}</p>
        <span class="legal-updated"><i class="fa-regular fa-calendar" aria-hidden="true"></i> Last updated {{ $updatedAt }}</span>
    </div>
</section>

<section class="container-xl legal-shell">
    <aside class="legal-aside">
        <p>On this page</p>
        <nav aria-label="Legal page navigation">
            <a href="#overview">Overview</a>
            <a href="#details">The details</a>
            <a href="#questions">Questions</a>
        </nav>
        <div class="legal-aside-card">
            <i class="fa-solid fa-shield-heart" aria-hidden="true"></i>
            <strong>Your career story matters.</strong>
            <span>We design every part of Resume Studio with clarity and trust in mind.</span>
        </div>
    </aside>

    <article class="legal-content">
        {{ $slot }}
    </article>
</section>
