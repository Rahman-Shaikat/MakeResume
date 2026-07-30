@php
    $editing = isset($homepageHero);
@endphp

<section class="admin-form-card">
    <div class="admin-form-card-heading">
        <div><span>Hero content</span><h2>Homepage introduction</h2></div>
        <p>Buttons continue to follow the secure guest and signed-in application flows.</p>
    </div>

    <div class="admin-form-grid">
        <x-admin.forms.input
            name="name"
            label="Configuration name"
            :value="old('name', $homepageHero->name ?? '')"
            placeholder="Example: Primary homepage hero"
            help="Internal label used only in the admin panel."
            required
        />

        <x-admin.forms.radio
            name="status"
            label="Publication status"
            :value="old('status', $homepageHero->status ?? 2)"
            :options="$statusOptions"
            help="Publishing this entry automatically unpublishes the current active hero."
            required
        />

        <x-admin.forms.input
            name="eyebrow"
            label="Eyebrow"
            :value="old('eyebrow', $homepageHero->eyebrow ?? '')"
            maxlength="120"
            required
        />

        <x-admin.forms.textarea
            name="headline"
            label="Headline"
            :value="old('headline', $homepageHero->headline ?? '')"
            rows="3"
            maxlength="255"
            required
        />

        <x-admin.forms.textarea
            name="description"
            label="Description"
            :value="old('description', $homepageHero->description ?? '')"
            rows="5"
            maxlength="2000"
            wrapper-class="admin-form-span"
            required
        />
    </div>
</section>

<section class="admin-form-card">
    <div class="admin-form-card-heading">
        <div><span>Resume visual</span><h2>Preview source</h2></div>
        <p>Choose a catalog template, upload a custom A4 image, or use both. A custom upload takes priority.</p>
    </div>

    <div class="admin-form-grid">
        <x-admin.forms.select
            name="resume_template_id"
            label="Catalog template"
            :value="old('resume_template_id', $homepageHero->resume_template_id ?? '')"
            :options="$resumeTemplates"
            placeholder="No catalog template selected"
            help="Uses the current thumbnail from the selected active template."
        />

        <x-admin.forms.file
            name="preview_image"
            label="Custom A4 preview image"
            :value="$homepageHero->preview_image_path ?? null"
            accept=".webp,.jpg,.jpeg,.png,image/webp,image/jpeg,image/png"
            help="Optional override. WebP recommended; genuine portrait A4, at least 700 × 990 px, maximum 2 MB."
        />
        @if ($editing && $homepageHero->preview_image_path)
            <div class="d-flex align-items-end pb-2">
                <x-admin.forms.checkbox
                    name="remove_preview_image"
                    label="Remove custom preview"
                    description="Use the selected catalog template instead."
                    :checked="old('remove_preview_image')"
                />
            </div>
        @endif
    </div>
</section>

<section class="admin-form-card">
    <div class="admin-form-card-heading">
        <div><span>Floating status cards</span><h2>Hero details</h2></div>
        <p>These small cards sit around the resume visual in the public hero.</p>
    </div>

    <div class="admin-form-grid">
        <x-admin.forms.input name="top_badge" label="Top badge" :value="old('top_badge', $homepageHero->top_badge ?? '')" maxlength="160" required />
        <x-admin.forms.input name="editor_title" label="Editor card title" :value="old('editor_title', $homepageHero->editor_title ?? '')" maxlength="160" required />
        <x-admin.forms.textarea name="editor_description" label="Editor card description" :value="old('editor_description', $homepageHero->editor_description ?? '')" rows="3" maxlength="255" required />
        <x-admin.forms.input name="bottom_status_title" label="Bottom status title" :value="old('bottom_status_title', $homepageHero->bottom_status_title ?? '')" maxlength="160" required />
        <x-admin.forms.input name="bottom_status_text" label="Bottom status text" :value="old('bottom_status_text', $homepageHero->bottom_status_text ?? '')" maxlength="160" required />
    </div>
</section>

<div class="admin-form-actions">
    <a href="{{ route('admin.homepage-heroes.index') }}" class="btn btn-light">Cancel</a>
    <button type="submit" class="btn btn-primary">{{ $editing ? 'Save hero' : 'Create hero' }}</button>
</div>
