<section class="admin-panel admin-narrow-panel">
    <div class="admin-panel-heading"><div><span>Route authorization</span><h2>Permission details</h2></div></div>
    <div class="admin-panel-body admin-form">
        <input type="hidden" name="type" value="1">

        <x-admin.forms.select
            name="group_id"
            label="Permission group"
            :value="old('group_id', $permission->group_id ?? '')"
            :options="$groups"
            placeholder="Select a group"
            data-permission-group-select
            required
        />

        <x-admin.forms.select
            name="parent_id"
            label="Hierarchy"
            :value="old('parent_id', $permission->parent_id ?? 0)"
            :options="[]"
            placeholder="Parent permission (module access)"
            placeholder-value="0"
            help="Parent permissions protect module access; child permissions protect create, update, delete, or other actions."
            data-permission-parent-select
            required
        >
            @foreach ($parentPermissions as $parentPermission)
                <option
                    value="{{ $parentPermission->id }}"
                    data-permission-group="{{ $parentPermission->group_id }}"
                    @selected((int) old('parent_id', $permission->parent_id ?? 0) === $parentPermission->id)
                >
                    Child of {{ $parentPermission->group?->name }} / {{ $parentPermission->name }}
                </option>
            @endforeach
        </x-admin.forms.select>

        <x-admin.forms.input
            name="name"
            label="Display name"
            :value="old('name', $permission->name ?? '')"
            maxlength="100"
            required
        />

        @if (isset($permission))
            <x-admin.forms.input
                name="meta_name"
                label="Permission key"
                :value="$permission->meta_name"
                help="This key is immutable because routes and middleware may already depend on it."
                readonly
            />
        @else
            <x-admin.forms.input
                name="meta_name"
                label="Permission key"
                :value="old('meta_name')"
                maxlength="100"
                pattern="[a-z0-9]+(?:-[a-z0-9]+)*"
                placeholder="example: users-update"
                help="Use lowercase kebab-case. Choose carefully; the key becomes immutable after creation."
                required
            />
        @endif

        <x-admin.forms.textarea
            name="short_desc"
            label="Description"
            :value="old('short_desc', $permission->short_desc ?? '')"
            rows="4"
            maxlength="255"
        />
    </div>
</section>

<div class="admin-form-actions admin-narrow-panel">
    <a href="{{ route('admin.permissions.index') }}" class="btn btn-light">Cancel</a>
    <button type="submit" class="btn btn-primary">{{ isset($permission) ? 'Save permission' : 'Create permission' }}</button>
</div>
