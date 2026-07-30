<section class="admin-panel">
    <div class="admin-panel-heading"><div><span>Website account</span><h2>User details</h2></div></div>
    <div class="admin-panel-body admin-form admin-form-columns">
        <x-admin.forms.input
            name="name"
            label="Full name"
            :value="old('name', $user->name ?? '')"
            maxlength="255"
            autocomplete="name"
            required
        />
        <x-admin.forms.input
            name="email"
            label="Email address"
            type="email"
            :value="old('email', $user->email ?? '')"
            maxlength="255"
            autocomplete="email"
            required
        />

        <x-admin.forms.radio
            name="verification_status"
            label="Email verification"
            :value="old('verification_status', isset($user) && $user->email_verified_at ? 1 : 2)"
            :options="$verificationOptions"
            wrapper-class="admin-form-span"
            required
        />

        @if (! isset($user))
            <x-admin.forms.input
                name="password"
                label="Password"
                type="password"
                minlength="8"
                maxlength="255"
                autocomplete="new-password"
                required
            />
            <x-admin.forms.input
                name="password_confirmation"
                label="Confirm password"
                type="password"
                minlength="8"
                maxlength="255"
                autocomplete="new-password"
                required
            />
        @endif
    </div>
</section>

<div class="admin-form-actions">
    <a href="{{ isset($user) ? route('admin.users.show', $user) : route('admin.users.index') }}" class="btn btn-light">Cancel</a>
    <button type="submit" class="btn btn-primary">{{ isset($user) ? 'Save user' : 'Create user' }}</button>
</div>
