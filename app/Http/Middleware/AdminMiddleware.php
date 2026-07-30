<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $admin = Auth::guard('admin')->user();

        if ($admin && (int) $admin->status === 1) {
            return $next($request);
        }

        if ($admin) {
            Auth::guard('admin')->logout();
        }

        return to_route('admin.loginpage')
            ->with('warning', 'Session expired. Please log in again or contact support for assistance.');
    }
}
