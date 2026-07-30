@extends('admin.layouts.app')

@section('title', 'Categories')

@section('content')
@php
    $canUpdateCategories = auth('admin')->user()->hasPermission('categories-update');
    $canReorderCategories = $reorderEnabled && $canUpdateCategories;
@endphp

<header class="admin-page-heading">
    <div>
        <span class="admin-eyebrow">Template management</span>
        <h1>Categories</h1>
        <p>Organize resume templates by job category and subcategory.</p>
    </div>
    @if (auth('admin')->user()->hasPermission('categories-create'))
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">Create category</a>
    @endif
</header>

<section class="admin-panel">
    <div class="admin-panel-heading admin-filter-heading">
        <div><span>Template taxonomy</span><h2>Category directory</h2></div>
        <form action="{{ route('admin.categories.index') }}" method="GET" class="admin-category-filters">
            <input class="form-control" name="search" value="{{ request('search') }}" placeholder="Search name or slug" aria-label="Search categories">
            <select class="form-select" name="level" aria-label="Filter by category level">
                <option value="">All levels</option>
                <option value="parent" @selected(request('level') === 'parent')>Parent categories</option>
                <option value="subcategory" @selected(request('level') === 'subcategory')>Subcategories</option>
            </select>
            <select class="form-select" name="status" aria-label="Filter by status">
                <option value="">All statuses</option>
                <option value="1" @selected(request('status') === '1')>Active</option>
                <option value="2" @selected(request('status') === '2')>Inactive</option>
            </select>
            <button class="btn btn-light" type="submit">Filter</button>
            @unless ($reorderEnabled)
                <a class="btn btn-light" href="{{ route('admin.categories.index') }}">Reset</a>
            @endunless
        </form>
    </div>

    @if ($canUpdateCategories)
        <div class="admin-sort-toolbar">
            <span class="admin-sort-grip" aria-hidden="true">⋮⋮</span>
            <p data-category-sort-status aria-live="polite">
                {{ $canReorderCategories ? 'Drag rows by the handle to change their display order.' : 'Clear all filters to enable drag-and-drop ordering.' }}
            </p>
        </div>
    @endif

    <div class="table-responsive">
        <table class="table admin-table admin-category-table mb-0">
            <thead>
                <tr>
                    <th class="admin-order-column">Order</th>
                    <th>Category</th>
                    <th>Parent</th>
                    <th>Position</th>
                    <th>Featured</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody
                @if ($canReorderCategories)
                    data-category-sortable
                    data-sort-url="{{ route('admin.categories.reorder') }}"
                @endif
            >
                @forelse ($categories as $category)
                    <tr data-category-id="{{ $category->id }}">
                        <td class="admin-order-column">
                            @if ($canReorderCategories)
                                <button class="admin-category-drag-handle" type="button" data-sort-handle aria-label="Drag {{ $category->name }} to a new position" title="Drag to reorder">
                                    <span aria-hidden="true">⋮⋮</span>
                                </button>
                            @else
                                <span class="admin-category-drag-placeholder" aria-hidden="true">—</span>
                            @endif
                        </td>
                        <td>
                            <strong>{{ $category->name }}</strong>
                            <small>
                                {{ $category->slug }}
                                @if ($category->parent_id === 0)
                                    · {{ $category->children_count }} active subcategories
                                @endif
                            </small>
                        </td>
                        <td>
                            @if ($category->parent)
                                <span class="admin-category-parent">{{ $category->parent->name }}</span>
                            @else
                                <span class="admin-badge is-super">Parent category</span>
                            @endif
                        </td>
                        <td data-category-position>{{ $category->position }}</td>
                        <td><span class="admin-badge {{ $category->is_featured === 1 ? 'is-featured' : '' }}">{{ $category->is_featured === 1 ? 'Featured' : 'Standard' }}</span></td>
                        <td><span class="admin-badge {{ $category->status === 1 ? 'is-active' : 'is-inactive' }}">{{ $category->status === 1 ? 'Active' : 'Inactive' }}</span></td>
                        <td>
                            <div class="admin-table-actions">
                                <x-admin.actions.edit
                                    :url="route('admin.categories.edit', $category)"
                                    permission="categories-update"
                                />
                                @if ($category->status === 1)
                                    <x-admin.actions.delete
                                        :url="route('admin.categories.destroy', $category)"
                                        permission="categories-delete"
                                        :confirm="$category->parent_id === 0 ? 'Deactivate this category and all active subcategories?' : 'Deactivate this subcategory?'"
                                    />
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="admin-empty-state">No categories match the current filters.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
