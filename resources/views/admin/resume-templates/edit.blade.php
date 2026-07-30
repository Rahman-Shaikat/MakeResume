@extends('admin.layouts.app')

@section('title', 'Edit resume template')

@section('content')
<header class="admin-page-heading">
    <div><span class="admin-eyebrow">Template management</span><h1>Edit {{ $resumeTemplate->name }}</h1><p>Update catalog presentation without exposing executable template files.</p></div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.templates.preview', $resumeTemplate) }}" class="btn btn-light" target="_blank" rel="noopener">Preview</a>
        <a href="{{ route('admin.templates.index') }}" class="btn btn-light">Back to templates</a>
    </div>
</header>

<form action="{{ route('admin.templates.update', $resumeTemplate) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PATCH')
    @include('admin.resume-templates.partials.form')
</form>
@endsection
