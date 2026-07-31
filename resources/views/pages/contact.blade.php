@extends('layouts.marketing', ['title' => 'Contact us - Resume Engineer'])

@section('content')
    <section class="legal-hero contact-hero">
        <div class="container-xl legal-hero-inner">
            <span class="legal-hero-eyebrow">We are here to help</span>
            <h1>Let’s make your next step easier.</h1>
            <p>Have a question about Resume Engineer, need help with your workspace, or want to share feedback? We would love to hear from you.</p>
        </div>
    </section>

    <section class="container-xl contact-shell">
        <div class="contact-support-grid">
            <article>
                <span><i class="fa-solid fa-life-ring" aria-hidden="true"></i></span>
                <h2>Account &amp; workspace</h2>
                <p>Need a hand signing in, managing a resume, or understanding a feature? Start with the details of what you are trying to do.</p>
            </article>
            <article>
                <span><i class="fa-solid fa-lightbulb" aria-hidden="true"></i></span>
                <h2>Ideas &amp; feedback</h2>
                <p>Great products improve through thoughtful feedback. Tell us what would make Resume Engineer more useful for you.</p>
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
                    <span><i class="fa-solid fa-phone" aria-hidden="true"></i> <a href="tel:+8801736769157">+880 1736 769157</a></span>
                    <span><i class="fa-solid fa-envelope" aria-hidden="true"></i> <a href="mailto:makeresume@gmail.com">makeresume@gmail.com</a></span>
                </div>
            </div>
            <form class="contact-form" action="{{ route('contact.store') }}" method="POST" aria-label="Contact Resume Engineer">
                @csrf

                @if (session('success'))
                    <div class="contact-form-alert contact-form-alert-success" role="status">
                        <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="contact-form-alert contact-form-alert-error" role="alert">
                        <i class="fa-solid fa-circle-exclamation" aria-hidden="true"></i>
                        <span>Please correct the highlighted fields and send your message again.</span>
                    </div>
                @endif

                <div class="contact-form-row">
                    <label>
                        <span>Your name</span>
                        <input type="text" name="name" value="{{ old('name') }}" autocomplete="name" maxlength="120" placeholder="Alex Morgan" required @error('name') aria-invalid="true" aria-describedby="contact-name-error" @enderror>
                        @error('name')
                            <small class="contact-field-error" id="contact-name-error">{{ $message }}</small>
                        @enderror
                    </label>
                    <label>
                        <span>Email address</span>
                        <input type="email" name="email" value="{{ old('email') }}" autocomplete="email" maxlength="255" placeholder="alex@example.com" required @error('email') aria-invalid="true" aria-describedby="contact-email-error" @enderror>
                        @error('email')
                            <small class="contact-field-error" id="contact-email-error">{{ $message }}</small>
                        @enderror
                    </label>
                </div>
                <label>
                    <span>What can we help with?</span>
                    <select name="topic" required @error('topic') aria-invalid="true" aria-describedby="contact-topic-error" @enderror>
                        <option value="" disabled @selected(! old('topic'))>Choose a topic</option>
                        <option value="account" @selected(old('topic') === 'account')>Account or workspace support</option>
                        <option value="feedback" @selected(old('topic') === 'feedback')>Feedback or feature idea</option>
                        <option value="partnership" @selected(old('topic') === 'partnership')>Partnership opportunity</option>
                        <option value="other" @selected(old('topic') === 'other')>Other</option>
                    </select>
                    @error('topic')
                        <small class="contact-field-error" id="contact-topic-error">{{ $message }}</small>
                    @enderror
                </label>
                <label>
                    <span>Your message</span>
                    <textarea name="message" rows="5" maxlength="5000" placeholder="Tell us a little more so we can point you in the right direction." required @error('message') aria-invalid="true" aria-describedby="contact-message-error" @enderror>{{ old('message') }}</textarea>
                    @error('message')
                        <small class="contact-field-error" id="contact-message-error">{{ $message }}</small>
                    @enderror
                </label>
                <label class="contact-honeypot" aria-hidden="true">
                    <span>Website</span>
                    <input type="text" name="website" tabindex="-1" autocomplete="off">
                </label>
                <p class="contact-form-note"><i class="fa-solid fa-circle-info" aria-hidden="true"></i> Your message goes directly to our support team. We will reply to the email address you provide.</p>
                <button type="submit" class="btn btn-primary contact-submit">
                    Send message <i class="fa-solid fa-paper-plane" aria-hidden="true"></i>
                </button>
            </form>
        </div>
    </section>
@endsection
