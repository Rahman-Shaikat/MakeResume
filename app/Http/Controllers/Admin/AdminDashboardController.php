<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use App\Models\Resume;
use App\Models\Role;
use App\Models\User;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard.index', [
            'totalUsers' => User::query()->count(),
            'totalResumes' => Resume::query()->count(),
            'activeAdministrators' => AdminUser::query()->where('status', 1)->count(),
            'activeRoles' => Role::query()->where('type', 1)->where('status', 1)->count(),
        ]);
    }
}
