<section class="admin-panel">
    <div class="admin-panel-heading"><div><span>Protected account</span><h2>Administrator details</h2></div></div>
    <div class="admin-panel-body admin-form admin-form-columns">
        <input type="hidden" name="country_id" value="{{ old('country_id', $adminUser->country_id ?? 0) }}">
        <div>
            <label class="form-label" for="name">Full name</label>
            <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $adminUser->name ?? '') }}" maxlength="100" required>
        </div>
        <div>
            <label class="form-label" for="email">Email address</label>
            <input class="form-control @error('email') is-invalid @enderror" id="email" name="email" type="email" value="{{ old('email', $adminUser->email ?? '') }}" maxlength="100" required>
        </div>
        <div>
            <label class="form-label" for="role_id">Role</label>
            <select class="form-select @error('role_id') is-invalid @enderror" id="role_id" name="role_id" required>
                <option value="">Select a role</option>
                @foreach ($roles as $role)
                    <option value="{{ $role->id }}" @selected((int) old('role_id', $adminUser->role_id ?? 0) === $role->id)>{{ $role->name }}{{ $role->status !== 1 ? ' (inactive)' : '' }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label" for="gender">Gender</label>
            <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender" required>
                @foreach ([0 => 'Prefer not to say', 1 => 'Male', 2 => 'Female', 3 => 'Other'] as $value => $label)
                    <option value="{{ $value }}" @selected((int) old('gender', $adminUser->gender ?? 0) === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label" for="phone">Phone</label>
            <input class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $adminUser->phone ?? '') }}" maxlength="20">
        </div>
        <div>
            <label class="form-label" for="image_path">Profile image</label>
            <input class="form-control @error('image_path') is-invalid @enderror" id="image_path" name="image_path" type="file" accept=".jpg,.jpeg,.png,.webp">
        </div>
        <div class="admin-form-span">
            <label class="form-label" for="address">Address</label>
            <input class="form-control @error('address') is-invalid @enderror" id="address" name="address" value="{{ old('address', $adminUser->address ?? '') }}" maxlength="255">
        </div>

        @if (! isset($adminUser))
            <div>
                <label class="form-label" for="password">Password</label>
                <input class="form-control @error('password') is-invalid @enderror" id="password" name="password" type="password" minlength="8" maxlength="50" autocomplete="new-password" required>
            </div>
            <div>
                <label class="form-label" for="password_confirmation">Confirm password</label>
                <input class="form-control" id="password_confirmation" name="password_confirmation" type="password" minlength="8" maxlength="50" autocomplete="new-password" required>
            </div>
        @else
            <div>
                <label class="form-label" for="status">Status</label>
                <select class="form-select" id="status" name="status" required>
                    <option value="1" @selected((int) old('status', $adminUser->status) === 1)>Active</option>
                    <option value="2" @selected((int) old('status', $adminUser->status) === 2)>Inactive</option>
                </select>
            </div>
        @endif

        <div>
            <label class="form-label" for="is_super">Access level</label>
            <select class="form-select" id="is_super" name="is_super" required>
                <option value="2" @selected((int) old('is_super', $adminUser->is_super ?? 2) === 2)>Use assigned role</option>
                <option value="1" @selected((int) old('is_super', $adminUser->is_super ?? 2) === 1)>Super administrator</option>
            </select>
            <small class="admin-field-help">Super administrators bypass every permission check.</small>
        </div>
    </div>
</section>

<div class="admin-form-actions">
    <a href="{{ route('admin.admin-users.index') }}" class="btn btn-light">Cancel</a>
    <button type="submit" class="btn btn-primary">{{ isset($adminUser) ? 'Save administrator' : 'Create administrator' }}</button>
</div>
