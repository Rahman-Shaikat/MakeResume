@extends('layouts.marketing', ['title' => 'Terms of service - Resume Engineer'])

@section('content')
    <x-legal-page
        eyebrow="Terms of service"
        title="A clear agreement for using Resume Engineer."
        summary="These terms explain the responsibilities that come with using our resume-building workspace and services."
        updated-at="July 30, 2026"
    >
        <section id="overview">
            <span class="legal-content-kicker">Using Resume Engineer</span>
            <h2>Welcome to your resume workspace.</h2>
            <p>By accessing or using Resume Engineer, you agree to these Terms of Service. If you do not agree, please do not use the service. These terms may be updated from time to time; continued use after an update means you accept the revised terms.</p>
            <div class="legal-highlight"><i class="fa-solid fa-circle-check" aria-hidden="true"></i><p>Use Resume Engineer responsibly, provide accurate information, and keep your account credentials secure.</p></div>
        </section>

        <section id="details">
            <h2>Your account</h2>
            <p>You are responsible for the information submitted through your account and for keeping access credentials confidential. Please notify us promptly if you believe your account has been accessed without permission.</p>

            <h2>Acceptable use</h2>
            <ul>
                <li>Use the service only for lawful purposes and in accordance with these terms.</li>
                <li>Do not attempt to interfere with, disrupt, or gain unauthorized access to Resume Engineer or its users.</li>
                <li>Do not upload content that infringes another person’s rights or violates applicable law.</li>
                <li>Do not use automated tools to scrape, overload, or misuse the service.</li>
            </ul>

            <h2>Your content</h2>
            <p>You retain ownership of the resume content you create. You grant us the limited permission needed to store, process, display, and deliver that content as part of operating Resume Engineer for you.</p>

            <h2>Our service</h2>
            <p>We work to keep Resume Engineer available and reliable, but the service may change, be updated, or be unavailable from time to time. We may suspend access when reasonably necessary to protect the service, users, or applicable legal requirements.</p>

            <h2>Disclaimers and limitations</h2>
            <p>Resume Engineer provides tools to help you create resumes; it does not guarantee interviews, job offers, or particular career outcomes. To the extent allowed by law, the service is provided on an “as is” and “as available” basis.</p>
        </section>

        <section id="questions" class="legal-questions">
            <i class="fa-regular fa-message" aria-hidden="true"></i>
            <div><h2>Questions about these terms?</h2><p>Our team can help clarify how these terms apply to your use of Resume Engineer.</p></div>
            <a href="{{ route('contact') }}" class="btn btn-outline-primary">Contact us</a>
        </section>
    </x-legal-page>
@endsection
