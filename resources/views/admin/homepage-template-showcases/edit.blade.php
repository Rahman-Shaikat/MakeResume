@extends('admin.layouts.app')

@section('title', 'Edit homepage template showcase')

@section('content')
<header class="admin-page-heading">
    <div><span class="admin-eyebrow">Website content</span><h1>Edit {{ $homepageTemplateShowcase->name }}</h1><p>Control the four template cards shown to visitors on the public homepage.</p></div>
    <div class="d-flex gap-2">
        <a href="{{ route('home') }}#templates" class="btn btn-light" target="_blank" rel="noopener">View section</a>
        <a href="{{ route('admin.homepage-template-showcases.index') }}" class="btn btn-light">Back to showcases</a>
    </div>
</header>

<form action="{{ route('admin.homepage-template-showcases.update', $homepageTemplateShowcase) }}" method="POST">
    @csrf
    @method('PATCH')
    @include('admin.homepage-template-showcases.partials.form')
</form>
@endsection
