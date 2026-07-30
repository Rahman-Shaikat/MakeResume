@extends('layouts.marketing', ['title' => 'About us - Resume Studio'])

@section('content')
    <section class="about-hero">
        <div class="container-xl about-hero-grid">
            <div>
                <span class="legal-hero-eyebrow">Built for the next chapter</span>
                <h1>Make every opportunity feel within reach.</h1>
                <p>Resume Studio gives ambitious professionals a calm, focused place to turn their experience into a resume they are proud to share.</p>
                <div class="about-hero-actions">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn btn-light">Open your workspace</a>
                    @else
                        <a href="{{ route('register') }}" class="btn btn-light">Create your resume <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                    @endauth
                    <a href="{{ route('contact') }}" class="about-link">Talk to us</a>
                </div>
            </div>

            <div class="about-hero-card" aria-label="Resume Studio promise">
                <span class="about-card-icon"><i class="fa-solid fa-sparkles" aria-hidden="true"></i></span>
                <p>One focused workspace for every version of your career story.</p>
                <div>
                    <span><i class="fa-solid fa-check" aria-hidden="true"></i> Professional templates</span>
                    <span><i class="fa-solid fa-check" aria-hidden="true"></i> Your content stays yours</span>
                    <span><i class="fa-solid fa-check" aria-hidden="true"></i> Designed for clarity</span>
                </div>
            </div>
        </div>
    </section>

    <section class="container-xl about-intro-section">
        <div class="about-intro-copy">
            <span class="section-kicker">Our point of view</span>
            <h2>A resume is more than a document.</h2>
            <p>It is the short, clear story of the work you have done and the value you are ready to create. We built Resume Studio to make shaping that story feel less overwhelming and more intentional.</p>
        </div>
        <div class="about-principles">
            <article>
                <span><i class="fa-solid fa-pen-ruler" aria-hidden="true"></i></span>
                <h3>Thoughtful by default</h3>
                <p>Guided structure and carefully crafted layouts keep the focus on your experience.</p>
            </article>
            <article>
                <span><i class="fa-solid fa-layer-group" aria-hidden="true"></i></span>
                <h3>Adaptable for every role</h3>
                <p>Create tailored versions for new roles without losing the work you have already done.</p>
            </article>
            <article>
                <span><i class="fa-solid fa-shield-heart" aria-hidden="true"></i></span>
                <h3>Respectfully designed</h3>
                <p>Your career information is personal. We treat it with the care it deserves.</p>
            </article>
        </div>
    </section>

    <section class="container-xl about-story-section">
        <div class="about-story-panel">
            <span class="section-kicker">Why we exist</span>
            <h2>Clear tools make confident decisions easier.</h2>
            <p>Career transitions ask a lot of people. Resume Studio removes unnecessary friction from one important step, so you can spend more energy on the opportunities ahead.</p>
            <a href="{{ route('contact') }}" class="text-link">Share feedback <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
        </div>
        <div class="about-stat-grid" aria-label="Resume Studio values">
            <article><strong>Clarity</strong><span>over clutter</span></article>
            <article><strong>Progress</strong><span>over perfection</span></article>
            <article><strong>People</strong><span>at the center</span></article>
            <article><strong>Trust</strong><span>at every step</span></article>
        </div>
    </section>
@endsection
