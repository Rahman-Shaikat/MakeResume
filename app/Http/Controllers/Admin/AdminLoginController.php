<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminLoginController extends Controller
{
    public function showAdminLoginForm(): View|RedirectResponse
    {
        if (Auth::guard('admin')->check()) {
            return to_route('admin.dashboard');
        }

        return view('admin.auth.login');
    }

    public function adminLogin(Request $request): RedirectResponse
    {
        if (Auth::guard('admin')->check()) {
            return to_route('admin.dashboard');
        }

        $credentials = $request->validate([
            'email' => ['required', 'email', 'max:100'],
            'password' => ['required', 'string', 'max:100'],
        ]);

        $admin = AdminUser::query()->where('email', $credentials['email'])->first();

        if (! $admin) {
            return back()->withErrors(['email' => 'Invalid credentials.'])->onlyInput('email');
        }

        if ($admin->status !== 1) {
            return back()->withErrors(['email' => 'Your account has been blocked.'])->onlyInput('email');
        }

        if (Auth::guard('admin')->attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->intended(route('admin.dashboard'))
                ->with('success', 'You are successfully logged in.');
        }

        return back()
            ->withErrors(['email' => 'The provided credentials do not match our records.'])
            ->onlyInput('email');
    }

    public function adminLogout(Request $request): RedirectResponse
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return to_route('admin.loginpage')->with('success', 'You have been signed out.');
    }
}
