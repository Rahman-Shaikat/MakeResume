# Service And Form Component Architecture Prompt

Use this prompt for a Codex agent working in another Laravel project. The goal is to recreate the clean architectural idea used in JannahStore: thin controllers, service classes for business/data workflow, Form Requests for validation, and reusable Blade components for all admin form fields.

The target project may not be eCommerce. Do not copy JannahStore modules blindly. Copy the organizational pattern and adapt names, services, components, and permissions to the target project's actual modules.

## Prompt For The Other Codex Agent

You are a senior Laravel backend and admin-panel engineer. Before implementing code, inspect the target Laravel project carefully: routes, controllers, models, migrations, requests, views, components, layouts, middleware, existing service classes, and admin modules.

Build the admin/backend architecture using these rules:

1. Keep controllers thin.
2. Put data loading, create/update/delete workflows, status changes, file upload handling, cache clearing, and reusable module logic into service classes.
3. Put validation into Form Request classes.
4. Put repeated form UI into Blade components.
5. Keep service naming, component naming, folders, and method names predictable.
6. Avoid unnecessary abstractions. Build only the services/components that are actually needed by the target project.

If the target project already has controllers where all functionality is written directly inside controller methods, refactor that logic into dedicated service classes. Do not leave business logic, query-heavy workflows, file upload handling, status transitions, or multi-step create/update/delete behavior inside controllers. The controller should become a small coordinator that validates the request, calls the correct service method, and returns the response.

## Service Class Pattern

JannahStore organizes services like this:

```text
app/Services/
app/Services/Admin/
app/Services/Api/
app/Services/Frontend/
```

Use the same idea:

- `App\Services\Admin` for admin-panel CRUD/workflow services.
- `App\Services\Api` for API-specific workflows.
- `App\Services\Frontend` for website/frontend read workflows.
- `App\Services` root for shared domain services used by more than one layer.

Do not put every service in the root folder. Namespace services by the layer that owns the workflow.

## Service Naming Convention

For admin CRUD modules, use:

```text
{ModuleName}CrudService
```

Examples:

```text
AdminUserCrudService
RoleCrudService
DepartmentCrudService
ProjectCrudService
InvoiceCrudService
SettingsCrudService
```

For shared business/domain logic, use:

```text
{DomainName}Service
```

Examples:

```text
NotificationService
ReportService
FileUploadService
SlugService
SettingsService
```

For API workflows, use:

```text
{FeatureName}Service
```

inside:

```text
app/Services/Api
```

Examples:

```text
Api/ProfileService.php
Api/CheckoutService.php
Api/SubscriptionService.php
```

Use singular domain names unless the module name is naturally plural in the project.

## Controller To Service Pattern

Controllers should mostly:

- receive a request
- call a Form Request when validation is needed
- call a service method
- return a view, redirect, or JSON response

Controllers should not contain:

- long Eloquent queries
- business calculations
- file upload storage logic
- cache invalidation
- transaction blocks
- repeated dropdown/list preparation
- status transition rules
- external API calls
- report aggregation
- permission assignment logic
- role/user workflow logic

When refactoring an existing controller, move each controller method's internal functionality to the dedicated service class method with the same responsibility. Keep route names, request classes, views, redirects, and response behavior unchanged unless a change is required for correctness.

Example:

```php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DepartmentRequest;
use App\Services\Admin\DepartmentCrudService;

class DepartmentController extends Controller
{
    public function index(DepartmentCrudService $service)
    {
        return view('admin.departments.index', $service->indexData());
    }

    public function create(DepartmentCrudService $service)
    {
        return view('admin.departments.create', $service->createData());
    }

    public function store(DepartmentRequest $request, DepartmentCrudService $service)
    {
        $result = $service->store($request->validated());

        return to_route('admin.departments.index')->with('success', $result['message']);
    }

    public function edit(int $id, DepartmentCrudService $service)
    {
        return view('admin.departments.edit', $service->editData($id));
    }

    public function update(DepartmentRequest $request, int $id, DepartmentCrudService $service)
    {
        $result = $service->update($id, $request->validated());

        return to_route('admin.departments.index')->with('success', $result['message']);
    }

    public function destroy(int $id, DepartmentCrudService $service)
    {
        $result = $service->delete($id);

        return back()->with('success', $result['message']);
    }
}
```

JannahStore often uses static service methods such as `BrandCrudService::listBrand()`. For a new project, prefer dependency injection as shown above. It is easier to test, easier to mock, and cleaner for larger systems. Keep static methods only for small stateless utility-style services.

## Admin CRUD Service Method Names

Use one predictable set of method names across modules:

```php
indexData()
createData()
store(array $data)
editData(int $id)
update(int $id, array $data)
delete(int $id)
changeStatus(int $id, int $status)
```

If a module has special flows, name them clearly:

```php
assignUser()
approve()
reject()
publish()
archive()
export()
```

Do not create inconsistent names like `listBrand()`, `createProduct()`, `addRole()`, and `getPaymentList()` in the new project. Pick one convention and use it everywhere.

## Service Return Shape

Admin service methods should return arrays that controllers can pass directly to views or redirects.

For view data:

```php
return [
    'departments' => Department::query()->latest()->paginate(20),
    'filters' => $filters,
];
```

For mutations:

```php
return [
    'success' => true,
    'message' => 'Department created successfully.',
    'model' => $department,
];
```

For API services, return typed arrays/resources from controllers, not Blade view data.

## Admin CRUD Service Example

```php
namespace App\Services\Admin;

use App\Models\Department;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DepartmentCrudService
{
    public function indexData(array $filters = []): array
    {
        return [
            'departments' => Department::query()
                ->when($filters['search'] ?? null, function ($query, $search) {
                    $query->where(function ($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%");
                    });
                })
                ->where('status', 1)
                ->latest()
                ->paginate(20)
                ->withQueryString(),
        ];
    }

    public function createData(): array
    {
        return [
            'statuses' => $this->statusOptions(),
        ];
    }

    public function store(array $data): array
    {
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);

        $department = Department::create($data);

        return [
            'success' => true,
            'message' => 'Department created successfully.',
            'model' => $department,
        ];
    }

    public function editData(int $id): array
    {
        return [
            'department' => Department::where('status', 1)->findOrFail($id),
            'statuses' => $this->statusOptions(),
        ];
    }

    public function update(int $id, array $data): array
    {
        $department = Department::where('status', 1)->findOrFail($id);
        $department->update($data);

        return [
            'success' => true,
            'message' => 'Department updated successfully.',
            'model' => $department,
        ];
    }

    public function delete(int $id): array
    {
        $department = Department::findOrFail($id);
        $department->update(['status' => 2]);

        return [
            'success' => true,
            'message' => 'Department deleted successfully.',
        ];
    }

    private function statusOptions(): array
    {
        return [
            ['id' => 1, 'name' => 'Active'],
            ['id' => 2, 'name' => 'Inactive'],
        ];
    }
}
```

## What Belongs In A Service

Put these in services:

- query building for index pages
- dropdown/list data for create/edit forms
- model creation/update/delete
- soft delete by status
- file uploads and old-file deletion
- slug generation
- cache invalidation
- stock/status transitions
- report aggregation
- external API calls
- transactional business workflows
- reusable calculations

Keep these out of services:

- HTML markup
- Blade rendering for normal admin pages
- raw request validation rules
- authorization checks that belong in middleware/policies
- unrelated utility functions

## Form Request Pattern

Use one Form Request per module where possible:

```text
app/Http/Requests/Admin/DepartmentRequest.php
app/Http/Requests/Admin/AdminUserRequest.php
app/Http/Requests/Admin/RoleRequest.php
```

Use route names or route parameters to adjust create/update validation.

Example:

```php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DepartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $departmentId = $this->route('department')?->id ?? $this->route('id');

        return [
            'name' => ['required', 'string', 'max:100'],
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('departments', 'code')->ignore($departmentId),
            ],
            'description' => ['nullable', 'string', 'max:500'],
            'status' => ['nullable', 'integer', 'in:1,2'],
        ];
    }
}
```

Do not validate inside the service unless the rule depends on runtime business state that cannot be expressed cleanly in the Form Request.

## Blade Component Organization

JannahStore organizes reusable components like this:

```text
resources/views/components/
resources/views/components/form-fields/
resources/views/components/form-fields/advanced/
resources/views/components/action-buttons/
resources/views/components/users/
resources/views/components/frontend/
```

For the new project, keep it more minimal:

```text
resources/views/components/
resources/views/components/admin/
resources/views/components/admin/forms/
resources/views/components/admin/actions/
resources/views/components/admin/layout/
resources/views/components/admin/table/
```

Recommended components:

```text
admin/forms/input.blade.php
admin/forms/textarea.blade.php
admin/forms/select.blade.php
admin/forms/select2.blade.php
admin/forms/checkbox.blade.php
admin/forms/radio.blade.php
admin/forms/file.blade.php
admin/forms/error.blade.php
admin/forms/group.blade.php
admin/actions/edit.blade.php
admin/actions/delete.blade.php
admin/actions/view.blade.php
admin/actions/status.blade.php
admin/layout/page-title.blade.php
admin/layout/breadcrumb.blade.php
admin/table/empty-state.blade.php
```

Do not duplicate basic and advanced components unless there is a real reason. Prefer one flexible component with clean props.

## Form Component API

Use short, consistent prop names. JannahStore uses props like `inputName`, `inputValue`, `inputRequired`, `labelText`, and `optionValueKey`. For a new project, use simpler names:

```text
name
label
type
value
required
placeholder
options
option-value
option-label
error-key
class
```

Example usage:

```blade
<x-admin.forms.input
    name="name"
    label="Name"
    :value="old('name', $department->name ?? '')"
    required
/>

<x-admin.forms.select
    name="status"
    label="Status"
    :value="old('status', $department->status ?? 1)"
    :options="$statuses"
    option-value="id"
    option-label="name"
    required
/>
```

## Minimal Input Component

Create:

```text
resources/views/components/admin/forms/input.blade.php
```

```blade
@props([
    'name',
    'label',
    'type' => 'text',
    'value' => null,
    'required' => false,
    'placeholder' => null,
    'errorKey' => null,
])

@php
    $field = $errorKey ?? $name;
    $id = str_replace(['.', '[', ']'], '_', $name);
@endphp

<div class="form-group">
    <label for="{{ $id }}" class="form-label {{ $required ? 'required' : '' }}">
        {{ $label }}
        @if($required)
            <span>*</span>
        @endif
    </label>

    <input
        {{ $attributes->merge(['class' => 'form-control' . ($errors->has($field) ? ' is-invalid' : '')]) }}
        id="{{ $id }}"
        type="{{ $type }}"
        name="{{ $name }}"
        value="{{ $value }}"
        placeholder="{{ $placeholder ?? $label }}"
        @required($required)
    >

    <x-admin.forms.error :field="$field" />
</div>
```

## Minimal Error Component

Create:

```text
resources/views/components/admin/forms/error.blade.php
```

```blade
@props(['field'])

@error($field)
    <span class="text-danger" role="alert">
        <strong>{{ $message }}</strong>
    </span>
@enderror
```

## Minimal Select Component

Create:

```text
resources/views/components/admin/forms/select.blade.php
```

```blade
@props([
    'name',
    'label',
    'value' => null,
    'options' => [],
    'optionValue' => 'id',
    'optionLabel' => 'name',
    'required' => false,
    'placeholder' => null,
    'errorKey' => null,
])

@php
    $field = $errorKey ?? $name;
    $id = str_replace(['.', '[', ']'], '_', $name);
@endphp

<div class="form-group">
    <label for="{{ $id }}" class="form-label {{ $required ? 'required' : '' }}">
        {{ $label }}
        @if($required)
            <span>*</span>
        @endif
    </label>

    <select
        {{ $attributes->merge(['class' => 'form-control form-select' . ($errors->has($field) ? ' is-invalid' : '')]) }}
        id="{{ $id }}"
        name="{{ $name }}"
        @required($required)
    >
        <option value="">{{ $placeholder ?? 'Select ' . $label }}</option>

        @foreach($options as $option)
            @php
                $currentValue = data_get($option, $optionValue);
                $currentLabel = data_get($option, $optionLabel);
            @endphp
            <option value="{{ $currentValue }}" @selected((string) $value === (string) $currentValue)>
                {{ $currentLabel }}
            </option>
        @endforeach
    </select>

    <x-admin.forms.error :field="$field" />
</div>
```

## Minimal Textarea Component

```blade
@props([
    'name',
    'label',
    'value' => null,
    'required' => false,
    'placeholder' => null,
    'rows' => 4,
    'errorKey' => null,
])

@php
    $field = $errorKey ?? $name;
    $id = str_replace(['.', '[', ']'], '_', $name);
@endphp

<div class="form-group">
    <label for="{{ $id }}" class="form-label {{ $required ? 'required' : '' }}">
        {{ $label }}
        @if($required)
            <span>*</span>
        @endif
    </label>

    <textarea
        {{ $attributes->merge(['class' => 'form-control' . ($errors->has($field) ? ' is-invalid' : '')]) }}
        id="{{ $id }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        placeholder="{{ $placeholder ?? $label }}"
        @required($required)
    >{{ $value }}</textarea>

    <x-admin.forms.error :field="$field" />
</div>
```

## Minimal Checkbox Component

```blade
@props([
    'name',
    'label',
    'value' => 1,
    'checked' => false,
    'errorKey' => null,
])

@php
    $field = $errorKey ?? $name;
    $id = str_replace(['.', '[', ']'], '_', $name . '_' . $value);
@endphp

<div class="form-check">
    <input
        {{ $attributes->merge(['class' => 'form-check-input' . ($errors->has($field) ? ' is-invalid' : '')]) }}
        id="{{ $id }}"
        type="checkbox"
        name="{{ $name }}"
        value="{{ $value }}"
        @checked($checked)
    >

    <label for="{{ $id }}" class="form-check-label">{{ $label }}</label>

    <x-admin.forms.error :field="$field" />
</div>
```

## File Upload Component

Only create a custom file component if the project uses file/image uploads in multiple forms.

Keep it minimal:

```blade
<x-admin.forms.file
    name="avatar"
    label="Profile Image"
    accept="image/*"
    :value="$admin->image_path ?? null"
/>
```

The component should:

- render the current image/link if `value` exists
- render a normal `<input type="file">`
- show validation errors
- not initialize a JS plugin unless the project actually uses one

Do not include Dropify, Select2, CKEditor, or other plugins by default. Add them only when the target project already includes those assets or explicitly needs them.

## Advanced Components Rule

Create advanced components only when a plain HTML field is not enough.

Examples:

- `select2.blade.php` only if large searchable dropdowns are required.
- `editor.blade.php` only if rich text editing is required.
- `date-picker.blade.php` only if the project uses a date picker plugin.
- `repeater.blade.php` only for dynamic nested forms.

Do not create both:

```text
forms/select.blade.php
forms/advanced/select.blade.php
```

unless their behavior is genuinely different.

## Action Button Components

JannahStore has reusable action buttons under:

```text
resources/views/components/action-buttons/
```

Use a cleaner structure:

```text
resources/views/components/admin/actions/edit.blade.php
resources/views/components/admin/actions/delete.blade.php
resources/views/components/admin/actions/view.blade.php
```

Each action component should accept:

```text
url
permission
label
icon
confirm
method
```

Example:

```blade
<x-admin.actions.edit
    :url="route('admin.departments.edit', $department)"
    permission="departments-update"
/>
```

Inside the component:

```blade
@props([
    'url',
    'permission' => null,
    'label' => 'Edit',
])

@php
    $admin = auth()->guard('admin')->user();
    $allowed = $admin && ((int) $admin->is_super === 1 || ! $permission || $admin->hasPermission($permission));
@endphp

@if($allowed)
    <a href="{{ $url }}" title="{{ $label }}" {{ $attributes->merge(['class' => 'btn btn-sm btn-success']) }}>
        {{ $label }}
    </a>
@endif
```

For delete actions in a new project, prefer forms with `DELETE` method instead of legacy GET delete links.

## Form Partial Organization

Use page-level form partials for complex modules:

```text
resources/views/admin/departments/_form.blade.php
resources/views/admin/departments/create.blade.php
resources/views/admin/departments/edit.blade.php
```

The `_form.blade.php` should use field components:

```blade
<x-admin.forms.input
    name="name"
    label="Name"
    :value="old('name', $department->name ?? '')"
    required
/>

<x-admin.forms.textarea
    name="description"
    label="Description"
    :value="old('description', $department->description ?? '')"
/>

<x-admin.forms.select
    name="status"
    label="Status"
    :value="old('status', $department->status ?? 1)"
    :options="$statuses"
    option-value="id"
    option-label="name"
/>
```

Do not build one component per business form unless the form is reused across multiple pages or is complex enough to justify it.

## Recommended Folder Structure

Use this structure in the target Laravel project:

```text
app/
  Http/
    Controllers/
      Admin/
        DepartmentController.php
    Requests/
      Admin/
        DepartmentRequest.php
  Services/
    Admin/
      DepartmentCrudService.php
    Api/
      ProfileService.php
    Frontend/
      HomePageService.php
    FileUploadService.php
    SlugService.php

resources/
  views/
    admin/
      departments/
        index.blade.php
        create.blade.php
        edit.blade.php
        _form.blade.php
    components/
      admin/
        forms/
          input.blade.php
          textarea.blade.php
          select.blade.php
          checkbox.blade.php
          radio.blade.php
          file.blade.php
          error.blade.php
        actions/
          edit.blade.php
          delete.blade.php
          view.blade.php
```

## Optimization Rules

Follow these rules while creating services and components:

- Use eager loading in service index methods when views access relationships.
- Use pagination for admin lists.
- Use `select()` for dropdown data when only `id` and `name` are needed.
- Use database transactions for multi-table writes.
- Use Form Requests instead of validating inside controllers.
- Use route model binding where it simplifies code.
- Keep component props minimal and obvious.
- Use `data_get()` in select components so options can be arrays or models.
- Avoid repeated JavaScript initialization inside every component unless necessary.
- Use `@once` or stacked scripts for plugin initialization.
- Avoid global static `$data` properties in services.
- Avoid raw HTML strings in services.
- Avoid building SQL queries in Blade views.

## Things To Improve Compared To JannahStore

JannahStore's existing approach works, but a new project should improve these areas:

- Prefer injected services over static service methods.
- Use consistent method names across every CRUD service.
- Prefer RESTful delete forms over GET delete links.
- Use one clean component per field type instead of duplicate basic/advanced versions.
- Use slugged permission `meta_name` values instead of display names.
- Add foreign keys and unique indexes where appropriate.
- Use typed method parameters and return types.
- Keep service methods small.
- Move file upload logic into a dedicated `FileUploadService` if multiple modules upload files.
- Move slug generation into a dedicated `SlugService` if multiple modules need unique slugs.

## Final Checklist For The Codex Agent

Before finishing the target project implementation, verify:

- controllers are thin
- services are named consistently
- services are in the correct namespace
- every admin module has a Form Request
- views use reusable field components
- no duplicated input markup exists across forms
- permission checks use consistent `meta_name` strings
- action buttons respect permissions
- admin routes are protected
- migrations and seeders run cleanly
- admin CRUD pages work
- validation errors render beside fields
- tests cover at least one CRUD module, auth, role permission checks, and blocked access
