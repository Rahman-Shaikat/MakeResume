@extends('admin.layouts.app')

@section('title', 'Create homepage template showcase')

@section('content')
<header class="admin-page-heading">
    <div><span class="admin-eyebrow">Website content</span><h1>Create template showcase</h1><p>Prepare four template cards and their accompanying homepage copy.</p></div>
    <a href="{{ route('admin.homepage-template-showcases.index') }}" class="btn btn-light">Back to showcases</a>
</header>

<form action="{{ route('admin.homepage-template-showcases.store') }}" method="POST">
    @csrf
    @include('admin.homepage-template-showcases.partials.form')
</form>
@endsection
