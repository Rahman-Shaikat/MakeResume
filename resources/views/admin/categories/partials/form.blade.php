<section class="admin-panel">
    <div class="admin-panel-heading"><div><span>Template taxonomy</span><h2>Category details</h2></div></div>
    <div class="admin-panel-body admin-form admin-form-columns">
        <x-admin.forms.input
            name="name"
            label="Category name"
            :value="old('name', $category->name ?? '')"
            maxlength="255"
            data-category-name
            required
        />
        <x-admin.forms.input
            name="slug"
            label="Slug"
            :value="old('slug', $category->slug ?? '')"
            maxlength="255"
            placeholder="Generated automatically when blank"
            help="Lowercase kebab-case, for example software-engineering."
            data-category-slug
        />
        <x-admin.forms.select2
            name="parent_id"
            label="Parent category"
            :value="old('parent_id', $category->parent_id ?? 0)"
            :options="$parentCategories"
            placeholder="None — top-level category"
            placeholder-value="0"
            search-placeholder="Search parent categories"
            help="Search by name. Only one subcategory level is supported."
            required
        />
        <x-admin.forms.radio
            name="status"
            label="Status"
            :value="$category->status ?? 1"
            :options="$statusOptions"
            help="Making a parent inactive also deactivates its active subcategories."
            required
        />
        <x-admin.forms.radio
            name="is_featured"
            label="Featured"
            :value="$category->is_featured ?? 2"
            :options="$featuredOptions"
            required
        />
        <x-admin.forms.textarea
            name="short_desc"
            label="Short description"
            :value="old('short_desc', $category->short_desc ?? '')"
            rows="5"
            maxlength="2000"
            wrapper-class="admin-form-span"
        />
    </div>
</section>

<div class="admin-form-actions">
    <a href="{{ route('admin.categories.index') }}" class="btn btn-light">Cancel</a>
    <button type="submit" class="btn btn-primary">{{ isset($category) ? 'Save category' : 'Create category' }}</button>
</div>
