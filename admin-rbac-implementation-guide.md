# Admin RBAC Implementation Guide

Use this document to implement the same administrator, role, and permission architecture from `JannahStore` in another Laravel project.

The target project is not eCommerce, so do not copy JannahStore's business permissions such as products, orders, coupons, or payments unless that project actually has those modules. Copy the RBAC architecture, table shapes, auth flow, middleware behavior, and CRUD workflow. Build the permission groups and permission names from the target project's real admin modules.

## Goal

Build a first-party admin authorization system with:

- separate `admin_users` table
- separate Laravel `admin` session guard
- `roles` table
- `permission_groups` table
- nested `permissions` table
- `role_permissions` pivot table
- super admin bypass through `admin_users.is_super`
- middleware route protection using permission `meta_name`
- role CRUD with permission checkbox assignment
- admin user CRUD with role assignment

Do not install Spatie Permission or another RBAC package unless the target project explicitly requires it.

## Current Architecture Pattern

JannahStore uses this model:

```text
admin_users.role_id -> roles.id
roles.id -> role_permissions.role_id
role_permissions.permission_id -> permissions.id
permissions.group_id -> permission_groups.id
permissions.parent_id -> permissions.id or 0
```

Authorization checks are string-based:

```php
$admin->hasPermission('users-update')
```

Routes are protected like this:

```php
Route::patch('/admin/users/{id}', [AdminUserController::class, 'update'])
    ->middleware('check-permission:users-update');
```

If `admin_users.is_super == 1`, the user bypasses all permission checks.

## Database Tables

Create the tables with the same data types and column semantics.

### `roles`

```php
Schema::create('roles', function (Blueprint $table) {
    $table->id();
    $table->string('name', 100);
    $table->string('short_desc')->nullable();
    $table->unsignedTinyInteger('type')->comment('1-Admin, 3-User');
    $table->unsignedTinyInteger('status')->default(1)->comment('1-Active, 2-Inactive');
    $table->timestamp('created_at')->useCurrent();
    $table->timestamp('updated_at')->nullable();
});
```

For the target project, use `type = 1` for admin roles. If the project has no customer/user role system, do not use `type = 3` anywhere except keeping the column for compatibility.

### `permission_groups`

```php
Schema::create('permission_groups', function (Blueprint $table) {
    $table->id();
    $table->string('name', 100);
    $table->unsignedTinyInteger('type')->default(1)->comment('1-Admin');
    $table->unsignedTinyInteger('status')->default(1)->comment('1-Active, 2-Inactive');
    $table->timestamp('created_at')->useCurrent();
    $table->timestamp('updated_at')->nullable();
});
```

Use one group per admin module area, for example:

```text
Administrator
Dashboard
Settings
Reports
Content
Customers
Staff
Projects
```

Choose names based on the target project.

### `permissions`

```php
Schema::create('permissions', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('group_id');
    $table->integer('parent_id')->default(0);
    $table->string('name', 100);
    $table->string('meta_name');
    $table->string('short_desc')->nullable();
    $table->unsignedTinyInteger('type')->default(1)->comment('1-Admin');
    $table->unsignedTinyInteger('status')->default(1)->comment('1-Active, 2-Inactive');
    $table->timestamp('created_at')->useCurrent();
    $table->timestamp('updated_at')->nullable();
});
```

`parent_id = 0` means top-level permission. Child permissions use the parent permission's `id`.

Recommended permission structure:

```text
Users
  users-create
  users-update
  users-delete

Roles
  roles-create
  roles-update
  roles-delete

Settings
  settings-update
```

Use `meta_name` as the stable permission key. It must match route middleware strings exactly.

Recommended naming:

```text
module
module-create
module-update
module-delete
module-view
module-export
module-approve
```

Examples:

```text
users
users-create
users-update
users-delete
reports
reports-export
settings
settings-update
```

### `role_permissions`

```php
Schema::create('role_permissions', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('role_id');
    $table->unsignedBigInteger('permission_id');
});
```

JannahStore does not use timestamps on this table.

Recommended improvement for the target project:

```php
$table->unique(['role_id', 'permission_id']);
```

Foreign keys are optional if the project follows JannahStore exactly. For a new project, prefer adding them:

```php
$table->foreign('role_id')->references('id')->on('roles')->cascadeOnDelete();
$table->foreign('permission_id')->references('id')->on('permissions')->cascadeOnDelete();
```

### `admin_users`

```php
Schema::create('admin_users', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('role_id');
    $table->unsignedBigInteger('country_id')->default(0);
    $table->string('name', 100);
    $table->string('email', 100);
    $table->string('address')->nullable();
    $table->unsignedTinyInteger('gender')->default(0)->comment('1-Male, 2-Female, 3-Others');
    $table->string('phone', 20)->nullable()->unique();
    $table->string('image_path')->nullable();
    $table->string('password');
    $table->unsignedTinyInteger('status')->default(1)->comment('1-Active, 2-Inactive');
    $table->unsignedTinyInteger('is_super')->default(2)->comment('1-Yes, 2-No');
    $table->timestamp('email_verified_at')->nullable();
    $table->timestamp('created_at')->useCurrent();
    $table->timestamp('updated_at')->nullable();
});
```

For a non-eCommerce project, `country_id`, `gender`, `phone`, `address`, and `image_path` may still be kept for compatibility with the JannahStore admin-user design. If the target project has no country table, keep `country_id` as unsigned big integer default `0`; do not add a foreign key.

Recommended improvement:

```php
$table->unique('email');
$table->foreign('role_id')->references('id')->on('roles')->restrictOnDelete();
```

If the project uses soft-deletion by `status = 2`, keep unique validation scoped to active users as described below.

## Migration Order

Create migrations in this order:

1. `roles`
2. `permission_groups`
3. `permissions`
4. `role_permissions`
5. `admin_users`

If keeping all RBAC tables in one migration, create `permission_groups` before `permissions`. In JannahStore's old migration, the table exists by the time seeders run, but for a clean new project this order is clearer.

The `down()` method must drop child/dependent tables first:

```php
Schema::dropIfExists('admin_users');
Schema::dropIfExists('role_permissions');
Schema::dropIfExists('permissions');
Schema::dropIfExists('permission_groups');
Schema::dropIfExists('roles');
```

## Models

### `AdminUser`

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class AdminUser extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $guarded = [];
    protected $table = 'admin_users';

    protected $hidden = [
        'password',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function hasPermission(string $permission): bool
    {
        if ((int) $this->is_super === 1) {
            return true;
        }

        if (! $this->relationLoaded('role')) {
            $this->load('role.permissions');
        } elseif ($this->role && ! $this->role->relationLoaded('permissions')) {
            $this->role->load('permissions');
        }

        return (bool) $this->role?->permissions->contains('meta_name', $permission);
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? false, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        });
    }
}
```

JannahStore's original `hasPermission()` directly reads `$this->role->permissions`. The guarded version above prevents null-role errors and avoids repeated lazy loads.

### `Role`

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'roles';

    public function rolePermissions()
    {
        return $this->hasMany(RolePermission::class, 'role_id');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions', 'role_id', 'permission_id');
    }
}
```

### `Permission`

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'permissions';

    public function children()
    {
        return $this->hasMany(Permission::class, 'parent_id');
    }

    public function group()
    {
        return $this->belongsTo(PermissionGroup::class, 'group_id');
    }
}
```

### `PermissionGroup`

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermissionGroup extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'permission_groups';

    public function permissions()
    {
        return $this->hasMany(Permission::class, 'group_id');
    }

    public function parentPermissions()
    {
        return $this->hasMany(Permission::class, 'group_id')->where('parent_id', 0);
    }
}
```

### `RolePermission`

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'role_permissions';

    public $timestamps = false;
}
```

## Authentication Guard

Add an `admin` guard to `config/auth.php`:

```php
'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],

    'admin' => [
        'driver' => 'session',
        'provider' => 'admin_users',
    ],
],

'providers' => [
    'users' => [
        'driver' => 'eloquent',
        'model' => App\Models\User::class,
    ],

    'admin_users' => [
        'driver' => 'eloquent',
        'model' => App\Models\AdminUser::class,
    ],
],
```

Admin login must use:

```php
Auth::guard('admin')->attempt($credentials)
Auth::guard('admin')->user()
Auth::guard('admin')->logout()
```

## Middleware

### Admin Session Middleware

Purpose:

- allow only logged-in active admins
- redirect unauthenticated admins to admin login
- block inactive admins

```php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::guard('admin')->user();

        if (! empty($user) && (int) $user->status === 1) {
            return $next($request);
        }

        return to_route('admin.loginpage')
            ->with('warning', 'Session expired. Please log in again or contact support for further assistance.');
    }
}
```

### Permission Middleware

Purpose:

- allow all routes for `is_super = 1`
- otherwise require the named permission

```php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $admin = $request->user('admin');

        abort_if(! $admin, 401);

        if ((int) $admin->is_super === 1 || $admin->hasPermission($permission)) {
            return $next($request);
        }

        abort(403, 'Unauthorized');
    }
}
```

Register aliases in Laravel 11/12 `bootstrap/app.php`:

```php
$middleware->alias([
    'admin' => \App\Http\Middleware\AdminMiddleware::class,
    'check-permission' => \App\Http\Middleware\CheckPermission::class,
]);
```

For Laravel 10 and older, register route middleware in `app/Http/Kernel.php`.

## Admin Login Flow

Create `AdminLoginController` with:

- `showAdminLoginForm()`
- `adminLogin(Request $request)`
- `adminLogout()`

Validation:

```php
$credentials = $request->validate([
    'email' => ['required', 'email', 'max:100'],
    'password' => ['required', 'string', 'max:100'],
]);
```

Login logic:

```php
$user = AdminUser::where('email', $credentials['email'])->first();

if (! $user) {
    return back()->withErrors(['email' => 'Invalid credentials.']);
}

if ((int) $user->status !== 1) {
    return back()->withErrors(['email' => 'Your account has been blocked.'])->onlyInput('email');
}

if (Auth::guard('admin')->attempt($credentials)) {
    $request->session()->regenerate();

    return to_route('admin.dashboard')->with('success', 'You are successfully logged in');
}

return back()->withErrors(['email' => 'The provided credentials do not match our records.'])->onlyInput('email');
```

Logout:

```php
Auth::guard('admin')->logout();
$request->session()->invalidate();
$request->session()->regenerateToken();
```

## Route Protection Pattern

Wrap all admin pages:

```php
Route::prefix('admin')->middleware(['admin'])->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])
        ->name('admin.dashboard');
});
```

Protect module index/read routes with the parent permission:

```php
Route::get('/users', [AdminUserController::class, 'index'])
    ->name('admin.users.index')
    ->middleware('check-permission:users');
```

Protect create/update/delete actions with child permissions:

```php
Route::get('/users/create', [AdminUserController::class, 'create'])
    ->middleware('check-permission:users-create');

Route::post('/users', [AdminUserController::class, 'store'])
    ->middleware('check-permission:users-create');

Route::get('/users/{adminUser}/edit', [AdminUserController::class, 'edit'])
    ->middleware('check-permission:users-update');

Route::patch('/users/{adminUser}', [AdminUserController::class, 'update'])
    ->middleware('check-permission:users-update');

Route::delete('/users/{adminUser}', [AdminUserController::class, 'destroy'])
    ->middleware('check-permission:users-delete');
```

## Role Management Behavior

Role list:

```php
Role::where('status', 1)->get();
```

Role create page:

```php
PermissionGroup::with('parentPermissions.children')
    ->where('type', 1)
    ->get();
```

Role store:

1. Validate `name`, `short_desc`, and `type`.
2. Create the role.
3. Insert submitted permission IDs into `role_permissions`.

Validation:

```php
return [
    'name' => 'required|string|max:100',
    'short_desc' => 'nullable|string|max:255',
    'type' => 'required|in:1',
    'permission' => 'nullable|array',
    'permission.*' => 'integer|exists:permissions,id',
];
```

Store assignment:

```php
$role = Role::create($request->validated());

if ($request->filled('permission')) {
    RolePermission::insert(
        collect($request->permission)->unique()->map(fn ($permissionId) => [
            'role_id' => $role->id,
            'permission_id' => $permissionId,
        ])->values()->all()
    );
}
```

Role update:

```php
$role->update($validated);

$newPermissionIds = collect($request->input('permission', []))
    ->map(fn ($id) => (int) $id)
    ->unique()
    ->values()
    ->all();

RolePermission::where('role_id', $role->id)
    ->whereNotIn('permission_id', $newPermissionIds)
    ->delete();

foreach ($newPermissionIds as $permissionId) {
    $role->rolePermissions()->updateOrCreate(
        ['permission_id' => $permissionId],
        ['role_id' => $role->id, 'permission_id' => $permissionId]
    );
}
```

Role delete should be soft by status:

```php
$role->update(['status' => 2]);
```

Before deleting/inactivating a role, the target project should prevent deleting the last super-admin role or a role assigned to active admins unless the product owner explicitly allows it.

## Permission Management Behavior

Permissions are managed in groups. Each group contains parent permissions and optional child permissions.

Example structure:

```php
[
    'name' => 'Administrator',
    'permissions' => [
        [
            'name' => 'Users',
            'meta_name' => 'users',
            'sub_permissions' => [
                ['name' => 'Create', 'meta_name' => 'users-create'],
                ['name' => 'Update', 'meta_name' => 'users-update'],
                ['name' => 'Delete', 'meta_name' => 'users-delete'],
            ],
        ],
    ],
]
```

Important adaptation rule:

- `name` is display text.
- `meta_name` is the route/middleware key.
- `meta_name` should be lowercase kebab-case and must not change after release unless routes are updated.

When creating permissions through a UI, generate `meta_name` from a dedicated input or slugify `name`. JannahStore currently stores `meta_name` equal to submitted `name` in its permission UI; for the new project, prefer stable slugs.

## Admin User Management Behavior

Admin list:

```php
AdminUser::filter(request(['search']))
    ->with('role')
    ->latest()
    ->paginate(20);
```

Admin create validation:

```php
return [
    'user_type' => 'required|in:1',
    'role_id' => 'required|integer|exists:roles,id',
    'country_id' => 'required|integer',
    'name' => 'required|string|max:255',
    'email' => [
        'required',
        'email',
        'max:100',
        Rule::unique('admin_users', 'email')->where(fn ($query) => $query->where('status', 1)),
    ],
    'gender' => 'required|in:1,2,3',
    'phone' => [
        'nullable',
        'string',
        'max:20',
        Rule::unique('admin_users', 'phone')->where(fn ($query) => $query->where('status', 1)),
    ],
    'address' => 'nullable|string|max:255',
    'password' => 'required|string|min:8|max:50',
    'password_confirmation' => 'same:password',
    'image_path' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp,svg|max:2048',
    'is_super' => 'nullable|in:1,2',
];
```

Admin update validation:

- same as create, but ignore current admin ID for unique email/phone
- password is not updated here
- `status` is required `in:1,2`
- update password through a separate form/action

Admin create:

```php
$validated['password'] = Hash::make($validated['password']);
$validated['is_super'] = $request->input('is_super', 2);
AdminUser::create($validated);
```

Admin delete:

```php
$adminUser->update(['status' => 2]);
```

Do not physically delete admin users by default because logs, activity, and ownership references may need historical identity.

## Seeder Requirements

Create seeders in this order:

1. `RoleSeeder`
2. `PermissionGroupSeeder`
3. `PermissionSeeder`
4. `RolePermissionSeeder`
5. `AdminUserSeeder`

Recommended initial role:

```php
Role::create([
    'name' => 'Super Admin',
    'type' => 1,
    'status' => 1,
]);
```

Recommended initial admin:

```php
AdminUser::create([
    'role_id' => 1,
    'country_id' => 0,
    'name' => 'Super Admin',
    'email' => env('ADMIN_EMAIL', 'admin@example.com'),
    'phone' => null,
    'gender' => 0,
    'password' => Hash::make(env('ADMIN_PASSWORD', 'password')),
    'status' => 1,
    'is_super' => 1,
    'email_verified_at' => now(),
]);
```

Do not commit real production credentials. For local/dev seeders, document the default login clearly.

`RolePermissionSeeder` should attach all admin permissions to role ID 1:

```php
$permissions = Permission::where('type', 1)->get();

$rows = $permissions->map(fn ($permission) => [
    'role_id' => 1,
    'permission_id' => $permission->id,
])->all();

RolePermission::insert($rows);
```

## Building Permissions for a Non-eCommerce Project

The Codex agent must inspect the target project's admin sidebar/routes/controllers first. Build permissions only for actual active admin modules.

For each module, create:

- one parent permission for viewing/listing the module
- child permissions only for actions that actually exist

Example for a CRM:

```text
Administrator
  roles
    roles-create
    roles-update
    roles-delete
  admin-users
    admin-users-create
    admin-users-update
    admin-users-delete

CRM
  leads
    leads-create
    leads-update
    leads-delete
    leads-assign
  customers
    customers-create
    customers-update
    customers-delete

Reports
  reports
    reports-export

Settings
  settings
    settings-update
```

Do not create placeholder permissions for future modules.

## Blade/UI Integration

Sidebar/menu rendering should check:

```php
auth()->guard('admin')->user()->is_super == 1
    || auth()->guard('admin')->user()->hasPermission('users')
```

Action buttons should check child permissions:

```php
@if($isSuper || auth()->guard('admin')->user()->hasPermission('users-update'))
    <a href="{{ route('admin.users.edit', $user->id) }}">Edit</a>
@endif
```

Role permission checkbox UI should load:

```php
PermissionGroup::with('parentPermissions.children')->where('type', 1)->get();
```

Submit checked permission IDs as:

```html
<input type="checkbox" name="permission[]" value="{{ $permission->id }}">
```

## Security Rules

Implement these protections:

- Only active admins can access admin routes.
- Super admins bypass permissions with `is_super = 1`.
- Normal admins must have the exact `permissions.meta_name` required by route middleware.
- Hash passwords with `Hash::make()`.
- Never expose password hashes in views/API responses.
- Keep admin login separate from customer/user login.
- Regenerate session after successful admin login.
- Invalidate session and regenerate CSRF token on logout.
- Prevent self-deactivation if it would lock out the only super admin.
- Prevent removing `is_super` from the last active super admin.
- Use POST/PATCH/DELETE for state-changing actions in the new project, even though JannahStore has some legacy GET delete routes.

## Testing Checklist

The target project must add feature tests for:

- admin login success
- inactive admin login blocked
- admin logout
- unauthenticated admin route redirects to login
- active admin can access dashboard
- normal admin without permission receives 403
- normal admin with permission can access protected route
- super admin can access all protected routes
- role create stores permissions
- role update adds/removes permissions correctly
- role delete sets `status = 2`
- admin user create hashes password
- admin user update preserves password
- duplicate active admin email is rejected
- last super admin cannot be disabled or demoted

## Implementation Order for the Target Codex Agent

1. Inspect the target project's admin modules, admin sidebar, and admin route files.
2. Decide permission groups and `meta_name` keys from actual active modules.
3. Create migrations using the table shapes above.
4. Create models and relationships.
5. Add `admin` guard/provider to `config/auth.php`.
6. Create and register `AdminMiddleware` and `CheckPermission`.
7. Build `AdminLoginController`.
8. Build role CRUD, including permission checkbox assignment.
9. Build admin user CRUD, including role assignment and password change.
10. Seed Super Admin role, project-specific permission groups, permissions, role-permission rows, and a first super admin.
11. Protect every admin route with `admin` middleware and the correct `check-permission:{meta_name}` middleware.
12. Hide sidebar/menu/action buttons based on the same permission keys.
13. Run migrations, seeders, route list, and feature tests.

## Do Not Copy Blindly

Do not copy these JannahStore-specific permissions unless the target project has the same modules:

```text
country
state
city
payment
category
product
product-tag
product-rating-review
order
flash-sale
coupon
sliders
social-platform
blogs
pages
general-setting
reports
product-bundles
```

Use the same RBAC structure, but choose permission groups and permission keys from the new project's real business features.

