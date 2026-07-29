<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

final class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user()->load('resume');

        return view('dashboard.index', [
            'user' => $user,
            'resume' => $user->resume,
            'templates' => config('resume_templates.catalog'),
        ]);
    }
}
