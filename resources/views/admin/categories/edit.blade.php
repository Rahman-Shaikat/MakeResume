@extends('admin.layouts.app')

@section('title', 'Edit category')

@section('content')
<header class="admin-page-heading">
    <div><span class="admin-eyebrow">Template management</span><h1>Edit {{ $category->name }}</h1><p>Update its hierarchy, presentation, order, and availability.</p></div>
    <a href="{{ route('admin.categories.index') }}" class="btn btn-light">Back to categories</a>
</header>

<form action="{{ route('admin.categories.update', $category) }}" method="POST">
    @csrf
    @method('PATCH')
    @include('admin.categories.partials.form')
</form>
@endsection
