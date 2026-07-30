@extends('admin.layouts.app')

@section('title', 'Homepage template showcase')

@section('content')
@php($admin = auth('admin')->user())

<header class="admin-page-heading">
    <div>
        <span class="admin-eyebrow">Website content</span>
        <h1>Homepage template showcase</h1>
        <p>Choose the four catalog templates and copy displayed in the template section of the public homepage.</p>
    </div>
    @if ($admin->hasPermission('homepage-template-showcases-create'))
        <a href="{{ route('admin.homepage-template-showcases.create') }}" class="btn btn-primary">Create showcase</a>
    @endif
</header>

<section class="admin-panel">
    <div class="admin-panel-heading">
        <div><span>Public homepage</span><h2>Showcase configurations</h2></div>
        <a href="{{ route('home') }}#templates" class="btn btn-light" target="_blank" rel="noopener">View section</a>
    </div>

    <div class="table-responsive">
        <table class="table admin-table mb-0">
            <thead>
                <tr>
                    <th>Configuration</th>
                    <th>Selected templates</th>
                    <th>Headline</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($showcases as $showcase)
                    <tr>
                        <td><strong>{{ $showcase->name }}</strong><small>Updated {{ $showcase->updated_at->diffForHumans() }}</small></td>
                        <td>
                            <div class="admin-template-tags">
                                @foreach ($showcase->templates as $template)
                                    <span>{{ $template->name }}</span>
                                @endforeach
                            </div>
                        </td>
                        <td><span class="admin-table-copy">{{ Str::limit($showcase->headline, 72) }}</span></td>
                        <td><span class="admin-badge {{ $showcase->status === 1 ? 'is-active' : 'is-inactive' }}">{{ $showcase->status === 1 ? 'Active' : 'Inactive' }}</span></td>
                        <td>
                            <div class="admin-table-actions">
                                <x-admin.actions.edit :url="route('admin.homepage-template-showcases.edit', $showcase)" permission="homepage-template-showcases-update" />
                                <x-admin.actions.delete
                                    :url="route('admin.homepage-template-showcases.destroy', $showcase)"
                                    permission="homepage-template-showcases-delete"
                                    label="Delete"
                                    confirm="Delete this homepage template showcase?"
                                />
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="admin-empty-state">No homepage template showcases have been created yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
