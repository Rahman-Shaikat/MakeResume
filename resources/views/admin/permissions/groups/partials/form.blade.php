<section class="admin-panel admin-narrow-panel">
    <div class="admin-panel-heading"><div><span>Permission organization</span><h2>Group details</h2></div></div>
    <div class="admin-panel-body admin-form">
        <input type="hidden" name="type" value="1">
        <div>
            <label class="form-label" for="name">Group name</label>
            <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $permissionGroup->name ?? '') }}" maxlength="100" required>
        </div>
        <div>
            <label class="form-label" for="short_desc">Description</label>
            <textarea class="form-control @error('short_desc') is-invalid @enderror" id="short_desc" name="short_desc" rows="4" maxlength="255">{{ old('short_desc', $permissionGroup->short_desc ?? '') }}</textarea>
        </div>
    </div>
</section>

<div class="admin-form-actions admin-narrow-panel">
    <a href="{{ route('admin.permissions.index') }}" class="btn btn-light">Cancel</a>
    <button type="submit" class="btn btn-primary">{{ isset($permissionGroup) ? 'Save group' : 'Create group' }}</button>
</div>
