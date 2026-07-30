<section class="admin-panel admin-narrow-panel">
    <div class="admin-panel-heading"><div><span>Permission organization</span><h2>Group details</h2></div></div>
    <div class="admin-panel-body admin-form">
        <input type="hidden" name="type" value="1">
        <x-admin.forms.input
            name="name"
            label="Group name"
            :value="old('name', $permissionGroup->name ?? '')"
            maxlength="100"
            required
        />
        <x-admin.forms.textarea
            name="short_desc"
            label="Description"
            :value="old('short_desc', $permissionGroup->short_desc ?? '')"
            rows="4"
            maxlength="255"
        />
    </div>
</section>

<div class="admin-form-actions admin-narrow-panel">
    <a href="{{ route('admin.permissions.index') }}" class="btn btn-light">Cancel</a>
    <button type="submit" class="btn btn-primary">{{ isset($permissionGroup) ? 'Save group' : 'Create group' }}</button>
</div>
