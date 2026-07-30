@php
    $editing = isset($homepageTemplateShowcase);
    $selectedTemplates = old('template_ids', $editing ? $homepageTemplateShowcase->templates->pluck('id')->all() : []);
@endphp

<section class="admin-form-card">
    <div class="admin-form-card-heading">
        <div><span>Section copy</span><h2>Template showcase</h2></div>
        <p>The button keeps the existing secure registration or workspace destination.</p>
    </div>

    <div class="admin-form-grid">
        <x-admin.forms.input
            name="name"
            label="Configuration name"
            :value="old('name', $homepageTemplateShowcase->name ?? '')"
            placeholder="Example: Primary template showcase"
            help="Internal label used only in the admin panel."
            required
        />
        <x-admin.forms.radio
            name="status"
            label="Publication status"
            :value="old('status', $homepageTemplateShowcase->status ?? 2)"
            :options="$statusOptions"
            help="Publishing this entry automatically unpublishes the current active showcase."
            required
        />
        <x-admin.forms.input name="eyebrow" label="Eyebrow" :value="old('eyebrow', $homepageTemplateShowcase->eyebrow ?? '')" maxlength="120" required />
        <x-admin.forms.textarea name="headline" label="Headline" :value="old('headline', $homepageTemplateShowcase->headline ?? '')" rows="3" maxlength="255" required />
        <x-admin.forms.input name="cta_label" label="Call-to-action label" :value="old('cta_label', $homepageTemplateShowcase->cta_label ?? '')" maxlength="120" required />
    </div>
</section>

<section class="admin-form-card">
    <div class="admin-form-card-heading">
        <div><span>Catalog templates</span><h2>Select four templates</h2></div>
        <p>Only active catalog templates can be featured. Cards follow the catalog display order.</p>
    </div>

    <div class="admin-form-grid">
        <x-admin.forms.multiselect2
            name="template_ids[]"
            label="Featured templates"
            :values="$selectedTemplates"
            :options="$resumeTemplates"
            error-key="template_ids"
            placeholder="Search and select exactly four templates"
            help="An active showcase requires exactly four templates."
            wrapper-class="admin-form-span"
        />
    </div>
</section>

<div class="admin-form-actions">
    <a href="{{ route('admin.homepage-template-showcases.index') }}" class="btn btn-light">Cancel</a>
    <button type="submit" class="btn btn-primary">{{ $editing ? 'Save showcase' : 'Create showcase' }}</button>
</div>
