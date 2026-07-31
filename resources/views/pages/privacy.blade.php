@extends('layouts.marketing', ['title' => 'Privacy policy - Resume Engineer'])

@section('content')
    <x-legal-page
        eyebrow="Privacy at Resume Engineer"
        title="Your career story is personal."
        summary="This policy explains what information Resume Engineer collects, why we use it, and the choices you have."
        updated-at="July 30, 2026"
    >
        <section id="overview">
            <span class="legal-content-kicker">Our commitment</span>
            <h2>Privacy should be easy to understand.</h2>
            <p>We collect only the information needed to provide Resume Engineer, improve the product, protect the service, and communicate with you. We do not sell your personal information.</p>
            <div class="legal-highlight"><i class="fa-solid fa-shield-heart" aria-hidden="true"></i><p>Your resume content belongs to you. You control the information you add and the resumes you create.</p></div>
        </section>

        <section id="details">
            <h2>Information we collect</h2>
            <div class="legal-detail-list">
                <article><h3>Account information</h3><p>When you register, we collect information such as your name and email address so we can create and secure your account.</p></article>
                <article><h3>Resume content</h3><p>We store the details, sections, images, and template choices you add so that your resumes can be created, edited, and displayed in your workspace.</p></article>
                <article><h3>Service and device data</h3><p>We may collect limited technical information, such as browser type, device details, and activity needed to operate, secure, and improve the service.</p></article>
            </div>

            <h2>How we use information</h2>
            <ul>
                <li>Provide, maintain, and secure your Resume Engineer account and workspace.</li>
                <li>Respond to support requests and communicate important service updates.</li>
                <li>Understand how the product performs and improve its reliability and usability.</li>
                <li>Meet legal obligations and prevent misuse of the service.</li>
            </ul>

            <h2>How information is shared</h2>
            <p>We do not sell personal information. We share data only with service providers that help us operate Resume Engineer, when required by law, or when necessary to protect our users, rights, and service. Those providers may use information only to perform services on our behalf.</p>

            <h2>Your choices</h2>
            <p>You can update the content in your workspace and delete resumes you no longer need. You may also contact us with questions about the information associated with your account.</p>
        </section>

        <section id="questions" class="legal-questions">
            <i class="fa-regular fa-message" aria-hidden="true"></i>
            <div><h2>Questions about privacy?</h2><p>We welcome questions about this policy and how Resume Engineer handles personal information.</p></div>
            <a href="{{ route('contact') }}" class="btn btn-outline-primary">Contact us</a>
        </section>
    </x-legal-page>
@endsection
