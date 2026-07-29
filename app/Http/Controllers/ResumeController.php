<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ProfileImageRequest;
use App\Http\Requests\ResumeBuilderRequest;
use App\Models\Resume;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

final class ResumeController extends Controller
{
    public function selectTemplate(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'template_slug' => ['required', 'string', 'in:'.implode(',', array_keys(config('resume_templates.catalog')))],
        ]);

        $resume = Resume::query()->updateOrCreate(
            ['user_id' => $request->user()->id],
            ['template_slug' => $validated['template_slug']],
        );

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Template selected successfully.',
                'template_slug' => $resume->template_slug,
                'preview_url' => route('resume.templates.show', $resume->template_slug),
                'builder_url' => route('resume.builder'),
            ]);
        }

        return back()->with('status', 'Template selected successfully.');
    }

    public function uploadProfileImage(ProfileImageRequest $request): RedirectResponse
    {
        $user = $request->user();
        $resume = Resume::query()->firstOrCreate(
            ['user_id' => $user->id],
            ['template_slug' => 'template-one'],
        );

        $file = $request->file('profile_image');
        $filename = Str::uuid().'.'.$file->extension();
        $path = $file->storeAs('images', $filename, 'public');

        if ($resume->profile_image && Str::startsWith($resume->profile_image, 'images/')) {
            Storage::disk('public')->delete($resume->profile_image);
        }

        $resume->update(['profile_image' => $path]);

        return back()->with('status', 'Profile photo updated successfully.');
    }

    public function builder(Request $request): View|RedirectResponse
    {
        $user = $request->user()->load('resume');

        if (! $user->resume) {
            return redirect()->route('dashboard')
                ->with('status', 'Select a resume template before continuing.');
        }

        return view('resumes.builder', [
            'user' => $user,
            'resume' => $user->resume,
            'template' => config("resume_templates.catalog.{$user->resume->template_slug}"),
            'content' => $this->contentFor($user, $user->resume),
        ]);
    }

    public function updateBuilder(ResumeBuilderRequest $request): RedirectResponse
    {
        $request->user()->resume->update([
            'content' => $request->validated(),
        ]);

        return redirect()->route('resume.builder')
            ->with('status', 'Personal details saved successfully.');
    }

    public function showTemplate(Request $request, string $template): View
    {
        abort_unless(array_key_exists($template, config('resume_templates.catalog')), 404);

        $user = $request->user()->load('resume');

        return view("resumes.templates.{$template}", [
            'user' => $user,
            'resume' => $user->resume,
            'data' => config("resume_templates.catalog.{$template}.sample"),
            'content' => $this->contentFor($user, $user->resume),
            'embedded' => $request->boolean('embed'),
        ]);
    }

    /**
     * @return array<string, string>
     */
    private function contentFor(User $user, ?Resume $resume): array
    {
        $sample = config('resume_templates.catalog.template-one.sample');

        return array_replace([
            'full_name' => $user->name,
            'professional_title' => $sample['title'],
            'email' => $user->email,
            'phone' => $sample['phone'],
            'location' => $sample['location'],
            'linkedin' => $sample['linkedin'],
            'github' => $sample['github'],
            'summary' => $sample['summary'],
        ], $resume?->content ?? []);
    }
}
