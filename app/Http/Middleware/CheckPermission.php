<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\AdminUser;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        /** @var AdminUser|null $admin */
        $admin = $request->user('admin');

        abort_if(! $admin, 401);

        if ($admin->is_super === 1 || $admin->hasPermission($permission)) {
            return $next($request);
        }

        abort(403, 'Unauthorized');
    }
}
