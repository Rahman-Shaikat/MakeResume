@extends('layouts.marketing', ['title' => 'Contact us - Resume Studio'])

@section('content')
    <section class="legal-hero contact-hero">
        <div class="container-xl legal-hero-inner">
            <span class="legal-hero-eyebrow">We are here to help</span>
            <h1>Let’s make your next step easier.</h1>
            <p>Have a question about Resume Studio, need help with your workspace, or want to share feedback? We would love to hear from you.</p>
        </div>
    </section>

    <section class="container-xl contact-shell">
        <div class="contact-direct-links">
            <a href="tel:+8801736769157">
                <span><i class="fa-solid fa-phone" aria-hidden="true"></i></span>
                <div>
                    <small>Call us</small>
                    <strong>+880 1736 769157</strong>
                    <em>Monday–Friday, 9:00 AM–6:00 PM</em>
                </div>
                <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i>
            </a>
            <a href="mailto:makeresume@gmail.com">
                <span><i class="fa-solid fa-envelope" aria-hidden="true"></i></span>
                <div>
                    <small>Gmail support</small>
                    <strong>makeresume@gmail.com</strong>
                    <em>We aim to reply within two business days</em>
                </div>
                <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i>
            </a>
        </div>

        <div class="contact-support-grid">
            <article>
                <span><i class="fa-solid fa-life-ring" aria-hidden="true"></i></span>
                <h2>Account &amp; workspace</h2>
                <p>Need a hand signing in, managing a resume, or understanding a feature? Start with the details of what you are trying to do.</p>
            </article>
            <article>
                <span><i class="fa-solid fa-lightbulb" aria-hidden="true"></i></span>
                <h2>Ideas &amp; feedback</h2>
                <p>Great products improve through thoughtful feedback. Tell us what would make Resume Studio more useful for you.</p>
            </article>
            <article>
                <span><i class="fa-solid fa-handshake" aria-hidden="true"></i></span>
                <h2>Partnerships</h2>
                <p>Interested in working together? Share a little about your organization and the opportunity you have in mind.</p>
            </article>
        </div>

        <div class="contact-panel">
            <div class="contact-panel-copy">
                <span class="section-kicker">Send a message</span>
                <h2>Tell us what you need.</h2>
                <p>Give us a few details and our team will review your message. We aim to respond to genuine support requests within two business days.</p>
                <div class="contact-expectations">
                    <span><i class="fa-solid fa-clock" aria-hidden="true"></i> Typical response: 1–2 business days</span>
                    <span><i class="fa-solid fa-lock" aria-hidden="true"></i> Your details are handled with care</span>
                </div>
            </div>
            <form class="contact-form" aria-label="Contact Resume Studio">
                <div class="contact-form-row">
                    <label>
                        <span>Your name</span>
                        <input type="text" name="name" autocomplete="name" placeholder="Alex Morgan">
                    </label>
                    <label>
                        <span>Email address</span>
                        <input type="email" name="email" autocomplete="email" placeholder="alex@example.com">
                    </label>
                </div>
                <label>
                    <span>What can we help with?</span>
                    <select name="topic">
                        <option>Choose a topic</option>
                        <option>Account or workspace support</option>
                        <option>Feedback or feature idea</option>
                        <option>Partnership opportunity</option>
                        <option>Other</option>
                    </select>
                </label>
                <label>
                    <span>Your message</span>
                    <textarea name="message" rows="5" placeholder="Tell us a little more so we can point you in the right direction."></textarea>
                </label>
                <p class="contact-form-note"><i class="fa-solid fa-circle-info" aria-hidden="true"></i> Contact message delivery will be available soon. For now, please include your details when you reach out through your account support channel.</p>
                <button type="button" class="btn btn-primary contact-submit" disabled>
                    Send message <i class="fa-solid fa-paper-plane" aria-hidden="true"></i>
                </button>
            </form>
        </div>
    </section>
@endsection
