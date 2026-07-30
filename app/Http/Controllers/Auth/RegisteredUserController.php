<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\Frontend\UserRegistrationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

final class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(
        RegisterRequest $request,
        UserRegistrationService $service,
    ): RedirectResponse {
        $service->register($request->validated());
        $request->session()->regenerate();

        return redirect()->route('verification.notice')
            ->with('status', 'verification-link-sent');
    }
}
