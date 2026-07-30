@extends('admin.layouts.app')

@section('title', 'Edit homepage hero')

@section('content')
<header class="admin-page-heading">
    <div><span class="admin-eyebrow">Website content</span><h1>Edit {{ $homepageHero->name }}</h1><p>Update the hero visual and messaging without changing application code.</p></div>
    <div class="d-flex gap-2">
        <a href="{{ route('home') }}" class="btn btn-light" target="_blank" rel="noopener">View homepage</a>
        <a href="{{ route('admin.homepage-heroes.index') }}" class="btn btn-light">Back to heroes</a>
    </div>
</header>

<form action="{{ route('admin.homepage-heroes.update', $homepageHero) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PATCH')
    @include('admin.homepage-heroes.partials.form')
</form>
@endsection
