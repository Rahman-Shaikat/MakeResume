<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\AdminDashboardService;
use Illuminate\View\View;

final class AdminDashboardController extends Controller
{
    public function index(AdminDashboardService $service): View
    {
        return view('admin.dashboard.index', $service->dashboardData());
    }
}
