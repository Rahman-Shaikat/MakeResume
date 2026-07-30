<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminLoginRequest;
use App\Services\Admin\AdminAuthenticationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

final class AdminLoginController extends Controller
{
    public function showAdminLoginForm(): View|RedirectResponse
    {
        if (Auth::guard('admin')->check()) {
            return to_route('admin.dashboard');
        }

        return view('admin.auth.login');
    }

    public function adminLogin(
        AdminLoginRequest $request,
        AdminAuthenticationService $service,
    ): RedirectResponse {
        if (Auth::guard('admin')->check()) {
            return to_route('admin.dashboard');
        }

        $result = $service->attempt($request->validated());

        if (! $result['success']) {
            return back()->withErrors(['email' => $result['message']])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'))
            ->with('success', $result['message']);
    }

    public function adminLogout(
        Request $request,
        AdminAuthenticationService $service,
    ): RedirectResponse {
        $service->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return to_route('admin.loginpage')->with('success', 'You have been signed out.');
    }
}
