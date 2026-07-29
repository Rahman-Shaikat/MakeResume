<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ProfileImageRequest;
use App\Http\Resources\ResumeBuilderResource;
use App\Models\Resume;
use App\Models\User;
use App\Services\ResumeBuilderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

final class ResumeController extends Controller
{
    public function __construct(
        private readonly ResumeBuilderService $builderService,
    ) {}

    public function selectTemplate(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'template_slug' => ['required', 'string', 'in:'.implode(',', array_keys(config('resume_templates.catalog')))],
        ]);

        $resume = $request->user()->resumes()->create([
            'template_slug' => $validated['template_slug'],
            'content' => [
                'full_name' => $request->user()->name,
                'email' => $request->user()->email,
            ],
        ]);

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
        $filename = Str::uuid().'.'.$file->extension();
        $path = $file->storeAs('images', $filename, 'public');

        if ($resume->profile_image && Str::startsWith($resume->profile_image, 'images/')) {
            Storage::disk('public')->delete($resume->profile_image);
        }

        $resume->update(['profile_image' => $path]);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Profile photo updated successfully.',
                'profile_image_url' => Storage::disk('public')->url($path),
            ]);
        }

        return back()->with('status', 'Profile photo updated successfully.');
    }

    public function removeProfileImage(Request $request, Resume $resume): JsonResponse|RedirectResponse
    {
        Gate::authorize('update', $resume);

        $profileImage = $resume->profile_image;

        if ($profileImage && Str::startsWith($profileImage, 'images/')) {
            Storage::disk('public')->delete($profileImage);
        }

        $resume->update(['profile_image' => null]);

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

        $profileImage = $resume->profile_image;

        $resume->delete();

        if ($profileImage && Str::startsWith($profileImage, 'images/')) {
            Storage::disk('public')->delete($profileImage);
        }

        return redirect()
            ->route('dashboard')
            ->with('status', 'Resume deleted successfully.');
    }

    public function builder(Request $request, Resume $resume): View
    {
        Gate::authorize('update', $resume);
        $user = $request->user();
        $resume = $this->builderService->load($resume);
        $payload = (new ResumeBuilderResource($resume))->resolve();
        $payload['content'] = $this->contentFor($user, $resume, $resume->template_slug);

        return view('resumes.builder', [
            'user' => $user,
            'resume' => $resume,
            'template' => config("resume_templates.catalog.{$resume->template_slug}"),
            'builderPayload' => $payload,
        ]);
    }

    public function showTemplate(Request $request, string $template): View
    {
        abort_unless(array_key_exists($template, config('resume_templates.catalog')), 404);

        $user = $request->user();

        return view("resumes.templates.{$template}", [
            'user' => $user,
            'resume' => null,
            'sections' => collect(),
            'data' => config("resume_templates.catalog.{$template}.sample"),
            'content' => $this->contentFor($user, null, $template),
            'embedded' => $request->boolean('embed'),
        ]);
    }

    public function showResume(Request $request, Resume $resume): View
    {
        Gate::authorize('view', $resume);
        $resume = $this->builderService->load($resume);
        $template = $resume->template_slug;

        abort_unless(array_key_exists($template, config('resume_templates.catalog')), 404);

        return view("resumes.templates.{$template}", [
            'user' => $request->user(),
            'resume' => $resume,
            'sections' => $resume->sections,
            'data' => config("resume_templates.catalog.{$template}.sample"),
            'content' => $this->contentFor($request->user(), $resume, $template),
            'embedded' => $request->boolean('embed'),
        ]);
    }

    /**
     * @return array<string, string>
     */
    private function contentFor(
        User $user,
        ?Resume $resume,
        string $templateSlug,
    ): array {
        $sample = config("resume_templates.catalog.{$templateSlug}.sample");

        return array_replace([
            'full_name' => $user->name,
            'professional_title' => $sample['title'],
            'email' => $user->email,
            'phone' => $sample['phone'],
            'location' => $sample['location'],
            'website' => '',
            'linkedin' => $sample['linkedin'],
            'github' => $sample['github'],
            'summary' => $sample['summary'],
        ], $resume?->content ?? []);
    }
}
