<div class="admin-form-grid">
    <section class="admin-panel">
        <div class="admin-panel-heading"><div><span>Role details</span><h2>Identity</h2></div></div>
        <div class="admin-panel-body admin-form">
            <input type="hidden" name="type" value="1">
            <div>
                <label class="form-label" for="name">Role name</label>
                <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $role->name ?? '') }}" maxlength="100" required>
            </div>
            <div>
                <label class="form-label" for="short_desc">Description</label>
                <textarea class="form-control @error('short_desc') is-invalid @enderror" id="short_desc" name="short_desc" rows="4" maxlength="255">{{ old('short_desc', $role->short_desc ?? '') }}</textarea>
            </div>
        </div>
    </section>

    <section class="admin-panel">
        <div class="admin-panel-heading"><div><span>Exact capabilities</span><h2>Permissions</h2></div></div>
        <div class="admin-panel-body admin-permission-groups">
            @forelse ($permissionGroups as $group)
                <fieldset class="admin-permission-group">
                    <legend>{{ $group->name }}</legend>
                    @foreach ($group->parentPermissions as $permission)
                        <div class="admin-permission-module">
                            <label class="form-check">
                                <input class="form-check-input" type="checkbox" name="permission[]" value="{{ $permission->id }}" data-permission-parent="{{ $permission->id }}" @checked(in_array($permission->id, old('permission', $selectedPermissions)))>
                                <span><strong>{{ $permission->name }}</strong><small>{{ $permission->short_desc }}</small></span>
                            </label>
                            @if ($permission->children->isNotEmpty())
                                <div class="admin-permission-children">
                                    @foreach ($permission->children as $child)
                                        <label class="form-check">
                                            <input class="form-check-input" type="checkbox" name="permission[]" value="{{ $child->id }}" data-permission-child="{{ $permission->id }}" @checked(in_array($child->id, old('permission', $selectedPermissions)))>
                                            <span>{{ $child->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </fieldset>
            @empty
                <p class="admin-empty-state">No active permissions are available.</p>
            @endforelse
        </div>
    </section>
</div>

<div class="admin-form-actions">
    <a href="{{ route('admin.roles.index') }}" class="btn btn-light">Cancel</a>
    <button type="submit" class="btn btn-primary">{{ isset($role) ? 'Save role' : 'Create role' }}</button>
</div>
