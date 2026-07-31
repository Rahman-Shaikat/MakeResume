<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreContactMessageRequest;
use App\Services\ContactMessageService;
use Illuminate\Http\RedirectResponse;

final class ContactMessageController extends Controller
{
    public function __invoke(
        StoreContactMessageRequest $request,
        ContactMessageService $service,
    ): RedirectResponse {
        $service->submit(
            $request->safe()->only(['name', 'email', 'topic', 'message']),
            $request->ip(),
            $request->userAgent(),
        );

        return to_route('contact')->with('success', 'Thanks — your message has been sent. Our team will reply within two business days.');
    }
}
