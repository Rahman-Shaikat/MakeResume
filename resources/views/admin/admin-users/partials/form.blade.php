<section class="admin-panel">
    <div class="admin-panel-heading"><div><span>Protected account</span><h2>Administrator details</h2></div></div>
    <div class="admin-panel-body admin-form admin-form-columns">
        <input type="hidden" name="country_id" value="{{ old('country_id', $adminUser->country_id ?? 0) }}">

        <x-admin.forms.input
            name="name"
            label="Full name"
            :value="old('name', $adminUser->name ?? '')"
            maxlength="100"
            required
        />
        <x-admin.forms.input
            name="email"
            label="Email address"
            type="email"
            :value="old('email', $adminUser->email ?? '')"
            maxlength="100"
            required
        />
        <x-admin.forms.select
            name="role_id"
            label="Role"
            :value="old('role_id', $adminUser->role_id ?? '')"
            :options="$roles"
            placeholder="Select a role"
            required
        />
        <x-admin.forms.select
            name="gender"
            label="Gender"
            :value="old('gender', $adminUser->gender ?? 0)"
            :options="$genderOptions"
            required
        />
        <x-admin.forms.input
            name="phone"
            label="Phone"
            :value="old('phone', $adminUser->phone ?? '')"
            maxlength="20"
        />
        <x-admin.forms.file
            name="image_path"
            label="Profile image"
            :value="$adminUser->image_path ?? null"
            accept=".jpg,.jpeg,.png,.webp"
            help="JPG, PNG, or WebP up to 2 MB."
        />
        <x-admin.forms.input
            name="address"
            label="Address"
            :value="old('address', $adminUser->address ?? '')"
            wrapper-class="admin-form-span"
            maxlength="255"
        />

        @if (! isset($adminUser))
            <x-admin.forms.input
                name="password"
                label="Password"
                type="password"
                minlength="8"
                maxlength="50"
                autocomplete="new-password"
                required
            />
            <x-admin.forms.input
                name="password_confirmation"
                label="Confirm password"
                type="password"
                minlength="8"
                maxlength="50"
                autocomplete="new-password"
                required
            />
        @else
            <x-admin.forms.select
                name="status"
                label="Status"
                :value="old('status', $adminUser->status)"
                :options="$statusOptions"
                required
            />
        @endif

        <x-admin.forms.select
            name="is_super"
            label="Access level"
            :value="old('is_super', $adminUser->is_super ?? 2)"
            :options="$accessOptions"
            help="Super administrators bypass every permission check."
            required
        />
    </div>
</section>

<div class="admin-form-actions">
    <a href="{{ route('admin.admin-users.index') }}" class="btn btn-light">Cancel</a>
    <button type="submit" class="btn btn-primary">{{ isset($adminUser) ? 'Save administrator' : 'Create administrator' }}</button>
</div>
