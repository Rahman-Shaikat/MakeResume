@extends('admin.layouts.app')

@section('title', 'Resume templates')

@section('content')
@php
    $admin = auth('admin')->user();
    $canUpdate = $admin->hasPermission('templates-update');
    $canReorder = $reorderEnabled && $canUpdate;
@endphp

<header class="admin-page-heading">
    <div><span class="admin-eyebrow">Template management</span><h1>Resume templates</h1><p>Manage safe catalog metadata, thumbnails, categories, and publication status.</p></div>
    @if ($admin->hasPermission('templates-create'))
        <a href="{{ route('admin.templates.create') }}" class="btn btn-primary">Create template</a>
    @endif
</header>

<section class="admin-panel">
    <div class="admin-panel-heading admin-filter-heading">
        <div><span>Catalog</span><h2>Template directory</h2></div>
        <form action="{{ route('admin.templates.index') }}" method="GET" class="admin-template-filters">
            <input class="form-control" name="search" value="{{ request('search') }}" placeholder="Search name or slug">
            <select class="form-select" name="status"><option value="">All statuses</option><option value="1" @selected(request('status') === '1')>Active</option><option value="2" @selected(request('status') === '2')>Inactive</option></select>
            <select class="form-select" name="category"><option value="">All categories</option>@foreach ($categories as $category)<option value="{{ $category->slug }}" @selected(request('category') === $category->slug)>{{ $category->name }}</option>@endforeach</select>
            <select class="form-select" name="ats"><option value="">Any ATS status</option><option value="1" @selected(request('ats') === '1')>ATS friendly</option><option value="2" @selected(request('ats') === '2')>Not ATS friendly</option></select>
            <select class="form-select" name="renderer"><option value="">All renderers</option>@foreach ($rendererOptions as $value => $label)<option value="{{ $value }}" @selected(request('renderer') === $value)>{{ $label }}</option>@endforeach</select>
            <button class="btn btn-light" type="submit">Filter</button>
            @unless ($reorderEnabled)<a class="btn btn-light" href="{{ route('admin.templates.index') }}">Reset</a>@endunless
        </form>
    </div>

    @if ($canUpdate)
        <div class="admin-sort-toolbar"><span class="admin-sort-grip" aria-hidden="true">⋮⋮</span><p data-template-sort-status aria-live="polite">{{ $canReorder ? 'Drag rows to update gallery order.' : 'Clear all filters to enable drag-and-drop ordering.' }}</p></div>
    @endif

    <div class="table-responsive">
        <table class="table admin-table admin-template-table mb-0">
            <thead><tr><th>Order</th><th>Template</th><th>Categories</th><th>Renderer</th><th>Usage</th><th>Status</th><th>Featured</th><th class="text-end">Actions</th></tr></thead>
            <tbody @if ($canReorder) data-template-sortable data-sort-url="{{ route('admin.templates.reorder') }}" @endif>
                @forelse ($templates as $template)
                    <tr data-template-id="{{ $template->id }}">
                        <td>@if ($canReorder)<button class="admin-category-drag-handle" type="button" data-sort-handle aria-label="Drag {{ $template->name }}"><span aria-hidden="true">⋮⋮</span></button>@else<span data-template-position>{{ $template->position }}</span>@endif</td>
                        <td>
                            <div class="admin-template-identity">
                                @if ($template->thumbnailUrl())
                                    <img src="{{ $template->thumbnailUrl() }}" alt="" loading="lazy">
                                @else
                                    <span class="admin-template-placeholder" style="--template-accent: {{ $template->accent_color }}">A4</span>
                                @endif
                                <span><strong>{{ $template->name }}</strong><small>{{ $template->slug }}</small></span>
                            </div>
                        </td>
                        <td><div class="admin-template-tags">@forelse ($template->categories as $category)<span>{{ $category->name }}</span>@empty<small>All templates</small>@endforelse</div></td>
                        <td><code>{{ $template->renderer_key }}</code><small class="d-block">{{ $template->is_ats_friendly === 1 ? 'ATS friendly' : 'Visual layout' }}</small></td>
                        <td>{{ $template->resumes_count }} {{ Str::plural('resume', $template->resumes_count) }}</td>
                        <td><span class="admin-badge {{ $template->status === 1 ? 'is-active' : 'is-inactive' }}">{{ $template->status === 1 ? 'Active' : 'Inactive' }}</span></td>
                        <td><span class="admin-badge {{ $template->is_featured === 1 ? 'is-featured' : '' }}">{{ $template->is_featured === 1 ? 'Featured' : 'Standard' }}</span></td>
                        <td><div class="admin-table-actions">
                            <x-admin.actions.view :url="route('admin.templates.preview', $template)" permission="templates" target="_blank" />
                            <x-admin.actions.edit :url="route('admin.templates.edit', $template)" permission="templates-update" />
                            <x-admin.actions.delete :url="route('admin.templates.destroy', $template)" permission="templates-delete" confirm="Delete this unused template permanently?" />
                        </div></td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="admin-empty-state">No templates match the current filters.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
