<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\Frontend\DashboardService;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class DashboardController extends Controller
{
    public function __invoke(Request $request, DashboardService $service): View
    {
        return view(
            'dashboard.index',
            $service->indexData($request->user(), $request->string('category')->trim()->toString() ?: null),
        );
    }
}
