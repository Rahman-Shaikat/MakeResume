@extends('admin.layouts.app')

@section('title', 'Categories')

@section('content')
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
            @if (request()->hasAny(['search', 'level', 'status']))
                <a class="btn btn-light" href="{{ route('admin.categories.index') }}">Reset</a>
            @endif
        </form>
    </div>
    <div class="table-responsive">
        <table class="table admin-table mb-0">
            <thead>
                <tr><th>Category</th><th>Parent</th><th>Position</th><th>Featured</th><th>Status</th><th class="text-end">Actions</th></tr>
            </thead>
            <tbody>
                @forelse ($categories as $category)
                    <tr>
                        <td>
                            <strong>{{ $category->name }}</strong>
                            <small>{{ $category->slug }}{{ $category->parent_id === 0 ? " · {$category->children_count} active subcategories" : '' }}</small>
                        </td>
                        <td>
                            @if ($category->parent)
                                <span class="admin-category-parent">{{ $category->parent->name }}</span>
                            @else
                                <span class="admin-badge is-super">Parent category</span>
                            @endif
                        </td>
                        <td>{{ $category->position }}</td>
                        <td><span class="admin-badge {{ $category->is_featured === 1 ? 'is-featured' : '' }}">{{ $category->is_featured === 1 ? 'Featured' : 'Standard' }}</span></td>
                        <td><span class="admin-badge {{ $category->status === 1 ? 'is-active' : 'is-inactive' }}">{{ $category->status === 1 ? 'Active' : 'Inactive' }}</span></td>
                        <td>
                            <div class="admin-table-actions">
                                @if (auth('admin')->user()->hasPermission('categories-update'))
                                    <a class="btn btn-sm btn-light" href="{{ route('admin.categories.edit', $category) }}">Edit</a>
                                @endif
                                @if ($category->status === 1 && auth('admin')->user()->hasPermission('categories-delete'))
                                    <form
                                        action="{{ route('admin.categories.destroy', $category) }}"
                                        method="POST"
                                        data-confirm="{{ $category->parent_id === 0 ? 'Deactivate this category and all active subcategories?' : 'Deactivate this subcategory?' }}"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" type="submit">Deactivate</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="admin-empty-state">No categories match the current filters.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @include('admin.partials.pagination', ['paginator' => $categories])
</section>
@endsection
