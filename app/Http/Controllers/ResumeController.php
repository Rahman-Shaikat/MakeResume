<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ChangeResumeTemplateRequest;
use App\Http\Requests\ProfileImageRequest;
use App\Http\Requests\SelectResumeTemplateRequest;
use App\Models\Resume;
use App\Services\Frontend\ResumeService;
use App\Services\TemplateCatalogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

final class ResumeController extends Controller
{
    public function __construct(
        private readonly ResumeService $service,
    ) {}

    public function selectTemplate(SelectResumeTemplateRequest $request): JsonResponse|RedirectResponse
    {
        $resume = $this->service->create(
            $request->user(),
            $request->validated('template_slug'),
        );

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Your new resume is ready to customize.',
                'resume_id' => $resume->id,
                'template_slug' => $resume->template_slug,
                'preview_url' => route('resume.preview', $resume),
                'builder_url' => route('resume.builder', $resume),
            ], 201);
        }

        return redirect()->route('resume.builder', $resume);
    }

    public function uploadProfileImage(
        ProfileImageRequest $request,
        Resume $resume,
    ): JsonResponse|RedirectResponse {
        Gate::authorize('update', $resume);
        $file = $request->file('profile_image');
        abort_unless($file instanceof UploadedFile, 422);
        $result = $this->service->storeProfileImage($resume, $file);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Profile photo updated successfully.',
                'profile_image_url' => $result['url'],
            ]);
        }

        return back()->with('status', 'Profile photo updated successfully.');
    }

    public function removeProfileImage(Request $request, Resume $resume): JsonResponse|RedirectResponse
    {
        Gate::authorize('update', $resume);
        $this->service->removeProfileImage($resume);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Profile photo removed successfully.',
                'profile_image_url' => null,
            ]);
        }

        return back()->with('status', 'Profile photo removed successfully.');
    }

    public function destroy(Resume $resume): RedirectResponse
    {
        Gate::authorize('delete', $resume);
        $this->service->delete($resume);

        return redirect()
            ->route('dashboard')
            ->with('status', 'Resume deleted successfully.');
    }

    public function builder(Request $request, Resume $resume): View
    {
        Gate::authorize('update', $resume);

        return view('resumes.builder', $this->service->builderData($request->user(), $resume));
    }

    public function templates(
        Request $request,
        Resume $resume,
        TemplateCatalogService $catalog,
    ): View {
        Gate::authorize('update', $resume);

        return view('resumes.templates.index', [
            'resume' => $resume,
            'templates' => $catalog->activeTemplates(),
        ]);
    }

    public function changeTemplate(
        ChangeResumeTemplateRequest $request,
        Resume $resume,
    ): RedirectResponse {
        Gate::authorize('update', $resume);
        $this->service->switchTemplate($resume, $request->validated('template_slug'));

        return to_route('resume.builder', $resume)
            ->with('status', 'Resume template changed successfully. Your content was preserved.');
    }

    public function showTemplate(Request $request, string $template): View
    {
        $data = $this->service->templateData($request->user(), $template, $request->boolean('embed'));

        return view($data['view'], $data);
    }

    public function showResume(Request $request, Resume $resume): View
    {
        Gate::authorize('view', $resume);

        $data = $this->service->previewData($request->user(), $resume, $request->boolean('embed'));

        return view($data['view'], $data);
    }
}
