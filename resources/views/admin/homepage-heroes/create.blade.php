@extends('admin.layouts.app')

@section('title', 'Create homepage hero')

@section('content')
<header class="admin-page-heading">
    <div><span class="admin-eyebrow">Website content</span><h1>Create homepage hero</h1><p>Prepare a new homepage visual and publish it only when it is ready.</p></div>
    <a href="{{ route('admin.homepage-heroes.index') }}" class="btn btn-light">Back to heroes</a>
</header>

<form action="{{ route('admin.homepage-heroes.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @include('admin.homepage-heroes.partials.form')
</form>
@endsection
