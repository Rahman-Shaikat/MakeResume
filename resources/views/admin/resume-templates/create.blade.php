@extends('admin.layouts.app')

@section('title', 'Create resume template')

@section('content')
<header class="admin-page-heading">
    <div><span class="admin-eyebrow">Template management</span><h1>Create resume template</h1><p>Publish a safe catalog entry backed by a developer-deployed renderer.</p></div>
    <a href="{{ route('admin.templates.index') }}" class="btn btn-light">Back to templates</a>
</header>

<form action="{{ route('admin.templates.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @include('admin.resume-templates.partials.form')
</form>
@endsection
