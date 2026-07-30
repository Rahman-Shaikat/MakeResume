<section class="admin-panel admin-narrow-panel">
    <div class="admin-panel-heading"><div><span>Route authorization</span><h2>Permission details</h2></div></div>
    <div class="admin-panel-body admin-form">
        <input type="hidden" name="type" value="1">
        <div>
            <label class="form-label" for="group_id">Permission group</label>
            <select class="form-select @error('group_id') is-invalid @enderror" id="group_id" name="group_id" data-permission-group-select required>
                <option value="">Select a group</option>
                @foreach ($groups as $group)
                    <option value="{{ $group->id }}" @selected((int) old('group_id', $permission->group_id ?? 0) === $group->id)>{{ $group->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label" for="parent_id">Hierarchy</label>
            <select class="form-select @error('parent_id') is-invalid @enderror" id="parent_id" name="parent_id" data-permission-parent-select required>
                <option value="0">Parent permission (module access)</option>
                @foreach ($parentPermissions as $parentPermission)
                    <option
                        value="{{ $parentPermission->id }}"
                        data-permission-group="{{ $parentPermission->group_id }}"
                        @selected((int) old('parent_id', $permission->parent_id ?? 0) === $parentPermission->id)
                    >
                        Child of {{ $parentPermission->group?->name }} / {{ $parentPermission->name }}
                    </option>
                @endforeach
            </select>
            <small class="admin-field-help">Parent permissions protect module access; child permissions protect create, update, delete, or other actions.</small>
        </div>
        <div>
            <label class="form-label" for="name">Display name</label>
            <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $permission->name ?? '') }}" maxlength="100" required>
        </div>
        <div>
            <label class="form-label" for="meta_name">Permission key</label>
            @if (isset($permission))
                <input class="form-control" id="meta_name" value="{{ $permission->meta_name }}" readonly>
                <small class="admin-field-help">This key is immutable because routes and middleware may already depend on it.</small>
            @else
                <input class="form-control @error('meta_name') is-invalid @enderror" id="meta_name" name="meta_name" value="{{ old('meta_name') }}" maxlength="100" pattern="[a-z0-9]+(?:-[a-z0-9]+)*" placeholder="example: users-update" required>
                <small class="admin-field-help">Use lowercase kebab-case. Choose carefully; the key becomes immutable after creation.</small>
            @endif
        </div>
        <div>
            <label class="form-label" for="short_desc">Description</label>
            <textarea class="form-control @error('short_desc') is-invalid @enderror" id="short_desc" name="short_desc" rows="4" maxlength="255">{{ old('short_desc', $permission->short_desc ?? '') }}</textarea>
        </div>
    </div>
</section>

<div class="admin-form-actions admin-narrow-panel">
    <a href="{{ route('admin.permissions.index') }}" class="btn btn-light">Cancel</a>
    <button type="submit" class="btn btn-primary">{{ isset($permission) ? 'Save permission' : 'Create permission' }}</button>
</div>
