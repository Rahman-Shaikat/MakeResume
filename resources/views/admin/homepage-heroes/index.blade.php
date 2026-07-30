@extends('admin.layouts.app')

@section('title', 'Homepage hero')

@section('content')
@php($admin = auth('admin')->user())

<header class="admin-page-heading">
    <div>
        <span class="admin-eyebrow">Website content</span>
        <h1>Homepage hero</h1>
        <p>Manage the resume visual, copy, and status cards shown at the top of the public homepage.</p>
    </div>
    @if ($admin->hasPermission('homepage-heroes-create'))
        <a href="{{ route('admin.homepage-heroes.create') }}" class="btn btn-primary">Create hero</a>
    @endif
</header>

<section class="admin-panel">
    <div class="admin-panel-heading">
        <div><span>Public homepage</span><h2>Hero configurations</h2></div>
        <a href="{{ route('home') }}" class="btn btn-light" target="_blank" rel="noopener">View homepage</a>
    </div>

    <div class="table-responsive">
        <table class="table admin-table mb-0">
            <thead>
                <tr>
                    <th>Configuration</th>
                    <th>Preview source</th>
                    <th>Headline</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($heroes as $hero)
                    <tr>
                        <td>
                            <strong>{{ $hero->name }}</strong>
                            <small>Updated {{ $hero->updated_at->diffForHumans() }}</small>
                        </td>
                        <td>
                            @if ($hero->preview_image_path)
                                <span class="admin-badge is-featured">Custom A4 image</span>
                            @elseif ($hero->resumeTemplate)
                                <span class="admin-category-parent">{{ $hero->resumeTemplate->name }}</span>
                            @else
                                <small>No preview source</small>
                            @endif
                        </td>
                        <td><span class="admin-table-copy">{{ Str::limit($hero->headline, 72) }}</span></td>
                        <td><span class="admin-badge {{ $hero->status === 1 ? 'is-active' : 'is-inactive' }}">{{ $hero->status === 1 ? 'Active' : 'Inactive' }}</span></td>
                        <td>
                            <div class="admin-table-actions">
                                <x-admin.actions.edit :url="route('admin.homepage-heroes.edit', $hero)" permission="homepage-heroes-update" />
                                <x-admin.actions.delete
                                    :url="route('admin.homepage-heroes.destroy', $hero)"
                                    permission="homepage-heroes-delete"
                                    label="Delete"
                                    confirm="Delete this homepage hero configuration?"
                                />
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="admin-empty-state">No homepage heroes have been created yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
