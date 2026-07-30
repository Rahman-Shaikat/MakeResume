@php($checkedPermissions = old('permission', $selectedPermissions))

<div class="admin-form-grid">
    <section class="admin-panel">
        <div class="admin-panel-heading"><div><span>Role details</span><h2>Identity</h2></div></div>
        <div class="admin-panel-body admin-form">
            <input type="hidden" name="type" value="1">
            <x-admin.forms.input
                name="name"
                label="Role name"
                :value="old('name', $role->name ?? '')"
                maxlength="100"
                required
            />
            <x-admin.forms.textarea
                name="short_desc"
                label="Description"
                :value="old('short_desc', $role->short_desc ?? '')"
                rows="4"
                maxlength="255"
            />
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
                            <x-admin.forms.checkbox
                                name="permission[]"
                                :label="$permission->name"
                                :description="$permission->short_desc"
                                :value="$permission->id"
                                :checked="in_array($permission->id, $checkedPermissions)"
                                error-key="permission"
                                :input-attributes="['data-permission-parent' => $permission->id]"
                            />
                            @if ($permission->children->isNotEmpty())
                                <div class="admin-permission-children">
                                    @foreach ($permission->children as $child)
                                        <x-admin.forms.checkbox
                                            name="permission[]"
                                            :label="$child->name"
                                            :value="$child->id"
                                            :checked="in_array($child->id, $checkedPermissions)"
                                            error-key="permission"
                                            :input-attributes="['data-permission-child' => $permission->id]"
                                        />
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </fieldset>
            @empty
                <p class="admin-empty-state">No active permissions are available.</p>
            @endforelse
            <x-admin.forms.error field="permission" />
        </div>
    </section>
</div>

<div class="admin-form-actions">
    <a href="{{ route('admin.roles.index') }}" class="btn btn-light">Cancel</a>
    <button type="submit" class="btn btn-primary">{{ isset($role) ? 'Save role' : 'Create role' }}</button>
</div>
