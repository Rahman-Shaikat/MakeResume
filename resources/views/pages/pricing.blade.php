@extends('layouts.marketing', ['title' => 'Pricing - Resume Studio'])

@section('content')
    @php
        $primaryUrl = auth()->check() ? route('dashboard') : route('register');
        $primaryLabel = auth()->check() ? 'Open your workspace' : 'Start building free';
    @endphp

    <section class="pricing-hero">
        <div class="pricing-hero-orbit pricing-hero-orbit-one" aria-hidden="true"></div>
        <div class="pricing-hero-orbit pricing-hero-orbit-two" aria-hidden="true"></div>
        <div class="container-xl pricing-hero-inner" data-reveal>
            <span class="pricing-eyebrow"><i class="fa-solid fa-sparkles" aria-hidden="true"></i> Simple, transparent pricing</span>
            <h1>Start with confidence. Upgrade when the moment is right.</h1>
            <p>Every plan gives you a calm, professional place to shape your experience into a resume you are proud to send.</p>
            <div class="pricing-hero-reassurance" aria-label="Pricing reassurance">
                <span><i class="fa-solid fa-circle-check" aria-hidden="true"></i> Free plan available</span>
                <span><i class="fa-solid fa-circle-check" aria-hidden="true"></i> No card to get started</span>
                <span><i class="fa-solid fa-circle-check" aria-hidden="true"></i> Keep your work as you grow</span>
            </div>
        </div>
    </section>

    <section class="pricing-plans-section" id="plans">
        <div class="container-xl">
            <div class="pricing-section-intro" data-reveal>
                <div>
                    <span class="section-kicker">Choose your pace</span>
                    <h2>Everything you need to move your story forward.</h2>
                </div>
                <p>Begin with the essentials, take a focused short-term pass, or unlock room for every version of your next move.</p>
            </div>

            <div class="pricing-plan-grid">
                <article class="pricing-plan-card" data-reveal>
                    <div class="pricing-plan-head">
                        <span class="pricing-plan-icon is-blue"><i class="fa-regular fa-compass" aria-hidden="true"></i></span>
                        <span class="pricing-plan-kicker">A thoughtful start</span>
                        <h3>Free</h3>
                        <p>Build a polished first version without any pressure.</p>
                    </div>
                    <div class="pricing-price"><strong>$0</strong><span>forever</span></div>
                    <ul class="pricing-feature-list">
                        <li><i class="fa-solid fa-check" aria-hidden="true"></i> One saved resume</li>
                        <li><i class="fa-solid fa-check" aria-hidden="true"></i> Essential resume sections</li>
                        <li><i class="fa-solid fa-check" aria-hidden="true"></i> Selected professional templates</li>
                        <li><i class="fa-solid fa-check" aria-hidden="true"></i> Save and return anytime</li>
                    </ul>
                    <a href="{{ $primaryUrl }}" class="btn btn-outline-primary pricing-plan-cta">{{ $primaryLabel }}</a>
                </article>

                <article class="pricing-plan-card pricing-plan-card-pass" data-reveal data-reveal-delay="1">
                    <div class="pricing-plan-head">
                        <span class="pricing-plan-icon is-mint"><i class="fa-solid fa-bolt" aria-hidden="true"></i></span>
                        <span class="pricing-plan-kicker">For a focused sprint</span>
                        <h3>Career Pass</h3>
                        <p>A short, flexible window to refine every application.</p>
                    </div>
                    <div class="pricing-price"><strong>$3.50</strong><span>one time · 7 days</span></div>
                    <ul class="pricing-feature-list">
                        <li><i class="fa-solid fa-check" aria-hidden="true"></i> Everything in Free</li>
                        <li><i class="fa-solid fa-check" aria-hidden="true"></i> All resume templates</li>
                        <li><i class="fa-solid fa-check" aria-hidden="true"></i> Multiple tailored versions</li>
                        <li><i class="fa-solid fa-check" aria-hidden="true"></i> Profile-photo layouts</li>
                    </ul>
                    <a href="{{ $primaryUrl }}" class="btn btn-outline-primary pricing-plan-cta">Explore the pass</a>
                </article>

                <article class="pricing-plan-card pricing-plan-card-featured" data-reveal data-reveal-delay="2">
                    <span class="pricing-popular-badge"><i class="fa-solid fa-star" aria-hidden="true"></i> Most flexible</span>
                    <div class="pricing-plan-head">
                        <span class="pricing-plan-icon is-indigo"><i class="fa-solid fa-rocket" aria-hidden="true"></i></span>
                        <span class="pricing-plan-kicker">For every opportunity</span>
                        <h3>Pro</h3>
                        <p>Keep building, tailoring, and improving without limits.</p>
                    </div>
                    <div class="pricing-price"><strong>$6.50</strong><span>per month · billed annually</span></div>
                    <span class="pricing-savings"><i class="fa-solid fa-arrow-trend-down" aria-hidden="true"></i> Save 30% with annual billing</span>
                    <ul class="pricing-feature-list">
                        <li><i class="fa-solid fa-check" aria-hidden="true"></i> Unlimited saved resumes</li>
                        <li><i class="fa-solid fa-check" aria-hidden="true"></i> Full template catalog</li>
                        <li><i class="fa-solid fa-check" aria-hidden="true"></i> Reorder and add custom sections</li>
                        <li><i class="fa-solid fa-check" aria-hidden="true"></i> Early access to new layouts</li>
                    </ul>
                    <a href="{{ $primaryUrl }}" class="btn btn-primary pricing-plan-cta">Choose Pro <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
                </article>
            </div>

            <p class="pricing-plan-note" data-reveal><i class="fa-solid fa-shield-heart" aria-hidden="true"></i> Your resume content remains yours, whichever plan you choose.</p>
        </div>
    </section>

    <section class="pricing-comparison-section">
        <div class="container-xl">
            <div class="pricing-comparison-heading" data-reveal>
                <span class="section-kicker">Compare with clarity</span>
                <h2>A clear view of what you can do.</h2>
                <p>Choose the amount of flexibility that fits your current job search.</p>
            </div>

            <div class="pricing-comparison-wrap" data-reveal>
                <div class="table-responsive">
                    <table class="pricing-comparison-table">
                        <thead>
                            <tr>
                                <th scope="col">Workspace tools</th>
                                <th scope="col">Free</th>
                                <th scope="col">Career Pass</th>
                                <th scope="col" class="is-pro">Pro</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><th scope="row">Saved resume versions</th><td>1</td><td>Unlimited for 7 days</td><td class="is-pro">Unlimited</td></tr>
                            <tr><th scope="row">Resume templates</th><td>Selected</td><td>All templates</td><td class="is-pro">All templates</td></tr>
                            <tr><th scope="row">Custom resume sections</th><td><i class="fa-solid fa-minus" aria-label="Not included"></i></td><td><i class="fa-solid fa-check" aria-label="Included"></i></td><td class="is-pro"><i class="fa-solid fa-check" aria-label="Included"></i></td></tr>
                            <tr><th scope="row">Profile-photo layouts</th><td><i class="fa-solid fa-minus" aria-label="Not included"></i></td><td><i class="fa-solid fa-check" aria-label="Included"></i></td><td class="is-pro"><i class="fa-solid fa-check" aria-label="Included"></i></td></tr>
                            <tr><th scope="row">New template releases</th><td>As available</td><td>Included</td><td class="is-pro">Early access</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <section class="pricing-faq-section">
        <div class="container-xl pricing-faq-grid">
            <div class="pricing-faq-copy" data-reveal>
                <span class="section-kicker">Helpful answers</span>
                <h2>Questions before you begin?</h2>
                <p>We have kept the details straightforward, so you can focus on the work that matters: your next application.</p>
                <a href="{{ route('contact') }}" class="text-link">Talk to our team <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
            </div>

            <div class="accordion pricing-accordion" id="pricing-faq" data-reveal data-reveal-delay="1">
                <div class="accordion-item">
                    <h3 class="accordion-header" id="pricing-question-one"><button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#pricing-answer-one" aria-expanded="true" aria-controls="pricing-answer-one">Can I begin on the free plan?</button></h3>
                    <div id="pricing-answer-one" class="accordion-collapse collapse show" aria-labelledby="pricing-question-one" data-bs-parent="#pricing-faq"><div class="accordion-body">Yes. Start with the essentials, build your first resume, and move to a plan with more flexibility whenever your job search calls for it.</div></div>
                </div>
                <div class="accordion-item">
                    <h3 class="accordion-header" id="pricing-question-two"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#pricing-answer-two" aria-expanded="false" aria-controls="pricing-answer-two">What is the Career Pass for?</button></h3>
                    <div id="pricing-answer-two" class="accordion-collapse collapse" aria-labelledby="pricing-question-two" data-bs-parent="#pricing-faq"><div class="accordion-body">It is designed for a concentrated application period, giving you a short window to work with the full template collection and create tailored versions.</div></div>
                </div>
                <div class="accordion-item">
                    <h3 class="accordion-header" id="pricing-question-three"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#pricing-answer-three" aria-expanded="false" aria-controls="pricing-answer-three">Will I lose my resume if I change plans?</button></h3>
                    <div id="pricing-answer-three" class="accordion-collapse collapse" aria-labelledby="pricing-question-three" data-bs-parent="#pricing-faq"><div class="accordion-body">No. Your work remains in your workspace. Plan access only changes which creation options are available to you.</div></div>
                </div>
                <div class="accordion-item">
                    <h3 class="accordion-header" id="pricing-question-four"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#pricing-answer-four" aria-expanded="false" aria-controls="pricing-answer-four">Can I change my plan later?</button></h3>
                    <div id="pricing-answer-four" class="accordion-collapse collapse" aria-labelledby="pricing-question-four" data-bs-parent="#pricing-faq"><div class="accordion-body">Yes. Choose the level of flexibility that fits your current goals, then adjust as your applications and career plans evolve.</div></div>
                </div>
            </div>
        </div>
    </section>

    <section class="container-xl pricing-final-section" data-reveal>
        <div class="pricing-final-panel">
            <div>
                <span class="pricing-eyebrow"><i class="fa-solid fa-wand-magic-sparkles" aria-hidden="true"></i> Your next application starts here</span>
                <h2>Build the version that opens the right door.</h2>
                <p>Start with the free plan today. Your most important story deserves a focused place to take shape.</p>
            </div>
            <a href="{{ $primaryUrl }}" class="btn btn-light">{{ $primaryLabel }} <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
        </div>
    </section>
@endsection
