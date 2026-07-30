@php
    $editing = isset($resumeTemplate);
    $selectedCategories = old('category_ids', $editing ? $resumeTemplate->categories->pluck('id')->all() : []);
@endphp

<section class="admin-panel">
    <div class="admin-panel-heading"><div><span>Safe catalog</span><h2>Template details</h2></div></div>
    <div class="admin-panel-body admin-form admin-form-columns">
        <x-admin.forms.input
            name="name"
            label="Template name"
            :value="old('name', $resumeTemplate->name ?? '')"
            maxlength="255"
            data-template-name
            required
        />

        @if ($editing)
            <x-admin.forms.input
                name="slug_display"
                label="Slug"
                :value="$resumeTemplate->slug"
                help="Stable identifier. It cannot be changed after creation."
                disabled
            />
        @else
            <x-admin.forms.input
                name="slug"
                label="Slug"
                :value="old('slug')"
                maxlength="255"
                placeholder="Generated automatically when blank"
                help="This becomes immutable after creation."
                data-template-slug
            />
        @endif

        <x-admin.forms.select
            name="renderer_key"
            label="Developer renderer"
            :value="old('renderer_key', $resumeTemplate->renderer_key ?? '')"
            :options="collect($rendererOptions)->map(fn ($label, $value) => ['value' => $value, 'label' => $label])"
            option-value="value"
            option-label="label"
            placeholder="Select an allowlisted renderer"
            help="Only code deployed by a developer can appear here."
            required
        />

        <x-admin.forms.input
            name="accent_color"
            label="Accent color"
            type="color"
            :value="old('accent_color', $resumeTemplate->accent_color ?? '#00B6CE')"
            help="A validated #RRGGBB color used by the selected renderer."
            required
        />

        <x-admin.forms.multiselect2
            name="category_ids[]"
            label="Categories"
            :values="$selectedCategories"
            :options="$categories"
            error-key="category_ids"
            placeholder="Search and select categories"
            help="Optional. A template may belong to multiple parent categories or subcategories."
            wrapper-class="admin-form-span"
        />

        <x-admin.forms.textarea
            name="short_desc"
            label="Short description"
            :value="old('short_desc', $resumeTemplate->short_desc ?? '')"
            rows="5"
            maxlength="2000"
            wrapper-class="admin-form-span"
        />

        <x-admin.forms.file
            name="thumbnail"
            label="A4 thumbnail"
            :value="$resumeTemplate->thumbnail_path ?? null"
            accept=".webp,.jpg,.jpeg,.png,image/webp,image/jpeg,image/png"
            help="WebP recommended. Genuine portrait A4 image, at least 700 × 990 px, maximum 2 MB."
            wrapper-class="admin-form-span"
        />

        <x-admin.forms.radio
            name="status"
            label="Status"
            :value="$resumeTemplate->status ?? 2"
            :options="$statusOptions"
            help="Activation requires both a thumbnail and a valid renderer."
            required
        />
        <x-admin.forms.radio
            name="is_featured"
            label="Featured"
            :value="$resumeTemplate->is_featured ?? 2"
            :options="$yesNoOptions"
            required
        />
        <x-admin.forms.radio
            name="is_ats_friendly"
            label="ATS friendly"
            :value="$resumeTemplate->is_ats_friendly ?? 1"
            :options="$yesNoOptions"
            required
        />
        <x-admin.forms.radio
            name="allows_profile_photo"
            label="Profile photo"
            :value="$resumeTemplate->allows_profile_photo ?? 1"
            :options="$yesNoOptions"
            required
        />
    </div>
</section>

<div class="admin-form-actions">
    <a href="{{ route('admin.templates.index') }}" class="btn btn-light">Cancel</a>
    <button type="submit" class="btn btn-primary">{{ $editing ? 'Save template' : 'Create template' }}</button>
</div>
