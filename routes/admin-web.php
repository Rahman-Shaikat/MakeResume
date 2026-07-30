<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminLoginController;
use App\Http\Controllers\Admin\AdminNotificationController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\PermissionGroupController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/login/Rgd64_HdgmsPl6_ndgbskj45-5737ioahrf92-ythuwe+jinv984v', [AdminLoginController::class, 'showAdminLoginForm'])
        ->name('loginpage');
    Route::post('/login/Rgd64_HdgmsPl6_ndgbskj45-5737ioahrf92-ythuwe+jinv984v', [AdminLoginController::class, 'adminLogin'])
        ->middleware('guest:admin')
        ->name('login');

    Route::middleware('admin')->group(function (): void {
        Route::post('/logout', [AdminLoginController::class, 'adminLogout'])->name('logout');
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::post('/notifications/read-all', [AdminNotificationController::class, 'markAllRead'])
            ->name('notifications.read-all');
        Route::post('/notifications/{notification}/open', [AdminNotificationController::class, 'open'])
            ->name('notifications.open');

        Route::get('/categories', [CategoryController::class, 'index'])
            ->middleware('check-permission:categories')
            ->name('categories.index');
        Route::get('/categories/create', [CategoryController::class, 'create'])
            ->middleware('check-permission:categories-create')
            ->name('categories.create');
        Route::post('/categories', [CategoryController::class, 'store'])
            ->middleware('check-permission:categories-create')
            ->name('categories.store');
        Route::patch('/categories/reorder', [CategoryController::class, 'reorder'])
            ->middleware('check-permission:categories-update')
            ->name('categories.reorder');
        Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])
            ->middleware('check-permission:categories-update')
            ->name('categories.edit');
        Route::patch('/categories/{category}', [CategoryController::class, 'update'])
            ->middleware('check-permission:categories-update')
            ->name('categories.update');
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])
            ->middleware('check-permission:categories-delete')
            ->name('categories.destroy');

        Route::get('/roles', [RoleController::class, 'index'])
            ->middleware('check-permission:roles')
            ->name('roles.index');
        Route::get('/roles/create', [RoleController::class, 'create'])
            ->middleware('check-permission:roles-create')
            ->name('roles.create');
        Route::post('/roles', [RoleController::class, 'store'])
            ->middleware('check-permission:roles-create')
            ->name('roles.store');
        Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])
            ->middleware('check-permission:roles-update')
            ->name('roles.edit');
        Route::patch('/roles/{role}', [RoleController::class, 'update'])
            ->middleware('check-permission:roles-update')
            ->name('roles.update');
        Route::delete('/roles/{role}', [RoleController::class, 'destroy'])
            ->middleware('check-permission:roles-delete')
            ->name('roles.destroy');

        Route::get('/permissions', [PermissionController::class, 'index'])
            ->middleware('check-permission:permissions')
            ->name('permissions.index');
        Route::get('/permissions/create', [PermissionController::class, 'create'])
            ->middleware('check-permission:permissions-create')
            ->name('permissions.create');
        Route::post('/permissions', [PermissionController::class, 'store'])
            ->middleware('check-permission:permissions-create')
            ->name('permissions.store');
        Route::get('/permissions/{permission}/edit', [PermissionController::class, 'edit'])
            ->middleware('check-permission:permissions-update')
            ->name('permissions.edit');
        Route::patch('/permissions/{permission}', [PermissionController::class, 'update'])
            ->middleware('check-permission:permissions-update')
            ->name('permissions.update');
        Route::delete('/permissions/{permission}', [PermissionController::class, 'destroy'])
            ->middleware('check-permission:permissions-delete')
            ->name('permissions.destroy');

        Route::get('/permission-groups/create', [PermissionGroupController::class, 'create'])
            ->middleware('check-permission:permissions-create')
            ->name('permission-groups.create');
        Route::post('/permission-groups', [PermissionGroupController::class, 'store'])
            ->middleware('check-permission:permissions-create')
            ->name('permission-groups.store');
        Route::get('/permission-groups/{permissionGroup}/edit', [PermissionGroupController::class, 'edit'])
            ->middleware('check-permission:permissions-update')
            ->name('permission-groups.edit');
        Route::patch('/permission-groups/{permissionGroup}', [PermissionGroupController::class, 'update'])
            ->middleware('check-permission:permissions-update')
            ->name('permission-groups.update');
        Route::delete('/permission-groups/{permissionGroup}', [PermissionGroupController::class, 'destroy'])
            ->middleware('check-permission:permissions-delete')
            ->name('permission-groups.destroy');

        Route::get('/administrators', [AdminUserController::class, 'index'])
            ->middleware('check-permission:admin-users')
            ->name('admin-users.index');
        Route::get('/administrators/create', [AdminUserController::class, 'create'])
            ->middleware('check-permission:admin-users-create')
            ->name('admin-users.create');
        Route::post('/administrators', [AdminUserController::class, 'store'])
            ->middleware('check-permission:admin-users-create')
            ->name('admin-users.store');
        Route::get('/administrators/{adminUser}/edit', [AdminUserController::class, 'edit'])
            ->middleware('check-permission:admin-users-update')
            ->name('admin-users.edit');
        Route::patch('/administrators/{adminUser}', [AdminUserController::class, 'update'])
            ->middleware('check-permission:admin-users-update')
            ->name('admin-users.update');
        Route::get('/administrators/{adminUser}/password', [AdminUserController::class, 'editPassword'])
            ->middleware('check-permission:admin-users-update')
            ->name('admin-users.password.edit');
        Route::patch('/administrators/{adminUser}/password', [AdminUserController::class, 'updatePassword'])
            ->middleware('check-permission:admin-users-update')
            ->name('admin-users.password.update');
        Route::delete('/administrators/{adminUser}', [AdminUserController::class, 'destroy'])
            ->middleware('check-permission:admin-users-delete')
            ->name('admin-users.destroy');

        Route::get('/users', [UserController::class, 'index'])
            ->middleware('check-permission:users')
            ->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])
            ->middleware('check-permission:users-create')
            ->name('users.create');
        Route::post('/users', [UserController::class, 'store'])
            ->middleware('check-permission:users-create')
            ->name('users.store');
        Route::get('/users/{user}', [UserController::class, 'show'])
            ->middleware('check-permission:users')
            ->name('users.show');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])
            ->middleware('check-permission:users-update')
            ->name('users.edit');
        Route::patch('/users/{user}', [UserController::class, 'update'])
            ->middleware('check-permission:users-update')
            ->name('users.update');
        Route::get('/users/{user}/password', [UserController::class, 'editPassword'])
            ->middleware('check-permission:users-update')
            ->name('users.password.edit');
        Route::patch('/users/{user}/password', [UserController::class, 'updatePassword'])
            ->middleware('check-permission:users-update')
            ->name('users.password.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])
            ->middleware('check-permission:users-delete')
            ->name('users.destroy');
    });
});
