<section class="admin-panel">
    <div class="admin-panel-heading"><div><span>Template taxonomy</span><h2>Category details</h2></div></div>
    <div class="admin-panel-body admin-form admin-form-columns">
        <div>
            <label class="form-label" for="name">Category name</label>
            <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $category->name ?? '') }}" maxlength="255" data-category-name required>
        </div>
        <div>
            <label class="form-label" for="slug">Slug</label>
            <input class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" value="{{ old('slug', $category->slug ?? '') }}" maxlength="255" placeholder="Generated automatically when blank" data-category-slug>
            <small class="admin-field-help">Lowercase kebab-case, for example software-engineering.</small>
        </div>
        <div>
            <label class="form-label" for="parent_id">Parent category</label>
            <select class="form-select @error('parent_id') is-invalid @enderror" id="parent_id" name="parent_id" required>
                <option value="0">None — top-level category</option>
                @foreach ($parentCategories as $parentCategory)
                    <option value="{{ $parentCategory->id }}" @selected((int) old('parent_id', $category->parent_id ?? 0) === $parentCategory->id)>{{ $parentCategory->name }}</option>
                @endforeach
            </select>
            <small class="admin-field-help">Only one subcategory level is supported.</small>
        </div>
        <div>
            <label class="form-label" for="position">Display position</label>
            <input class="form-control @error('position') is-invalid @enderror" id="position" name="position" type="number" value="{{ old('position', $category->position ?? 0) }}" min="0" max="4294967295" required>
            <small class="admin-field-help">Lower numbers appear first.</small>
        </div>
        <div>
            <label class="form-label" for="status">Status</label>
            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                <option value="1" @selected((int) old('status', $category->status ?? 1) === 1)>Active</option>
                <option value="2" @selected((int) old('status', $category->status ?? 1) === 2)>Inactive</option>
            </select>
            <small class="admin-field-help">Making a parent inactive also deactivates its active subcategories.</small>
        </div>
        <div>
            <label class="form-label" for="is_featured">Featured</label>
            <select class="form-select @error('is_featured') is-invalid @enderror" id="is_featured" name="is_featured" required>
                <option value="2" @selected((int) old('is_featured', $category->is_featured ?? 2) === 2)>No</option>
                <option value="1" @selected((int) old('is_featured', $category->is_featured ?? 2) === 1)>Yes</option>
            </select>
        </div>
        <div class="admin-form-span">
            <label class="form-label" for="short_desc">Short description</label>
            <textarea class="form-control @error('short_desc') is-invalid @enderror" id="short_desc" name="short_desc" rows="5" maxlength="2000">{{ old('short_desc', $category->short_desc ?? '') }}</textarea>
        </div>
    </div>
</section>

<div class="admin-form-actions">
    <a href="{{ route('admin.categories.index') }}" class="btn btn-light">Cancel</a>
    <button type="submit" class="btn btn-primary">{{ isset($category) ? 'Save category' : 'Create category' }}</button>
</div>
