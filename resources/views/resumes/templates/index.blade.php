@extends('layouts.app', ['title' => 'Change Resume Template'])

@section('content')
<div class="dashboard-hero compact-dashboard-hero">
    <div class="container-xl">
        <span class="dashboard-kicker">Design only</span>
        <h1>Change your resume template</h1>
        <p>Your content, section order, custom sections, and profile photo will stay exactly as they are.</p>
    </div>
</div>

<div class="container-xl dashboard-content">
    <section class="panel-card">
        <div class="panel-heading">
            <div>
                <span class="section-kicker">Active templates</span>
                <h2>Choose a new design</h2>
                <p>Only the visual renderer and template slug will change.</p>
            </div>
            <a href="{{ route('resume.builder', $resume) }}" class="btn btn-light">Back to builder</a>
        </div>

        <div class="template-grid template-switch-grid">
            @foreach ($templates as $template)
                <x-template-card :template="$template" mode="switch" :resume="$resume" />
            @endforeach
        </div>
    </section>
</div>
@endsection
