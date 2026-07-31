<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\PublicTemplatePreviewService;
use Illuminate\Http\Response;

final class PublicTemplatePreviewController extends Controller
{
    public function __invoke(string $template, PublicTemplatePreviewService $previews): Response
    {
        $data = $previews->data($template);

        return response()
            ->view($data['view'], $data)
            ->header('Cache-Control', 'public, max-age=3600')
            ->header('Vary', 'Accept-Encoding');
    }
}
