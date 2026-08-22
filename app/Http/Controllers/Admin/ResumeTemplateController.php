<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReorderResumeTemplatesRequest;
use App\Http\Requests\Admin\StoreResumeTemplateRequest;
use App\Http\Requests\Admin\UpdateResumeTemplateRequest;
use App\Models\ResumeTemplate;
use App\Services\Admin\ResumeTemplateCrudService;
use App\Services\TemplateRendererRegistry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class ResumeTemplateController extends Controller
{
    public function index(Request $request, ResumeTemplateCrudService $service): View
    {
        return view(
            'admin.resume-templates.index',
            $service->indexData($request->only(['search', 'status', 'category', 'ats', 'renderer'])),
        );
    }

    public function create(ResumeTemplateCrudService $service): View
    {
        return view('admin.resume-templates.create', $service->createData());
    }

    public function store(
        StoreResumeTemplateRequest $request,
        ResumeTemplateCrudService $service,
    ): RedirectResponse {
        $result = $service->store($request->validated(), $request->file('thumbnail'));

        return to_route('admin.templates.edit', $result['model'])->with('success', $result['message']);
    }

    public function edit(ResumeTemplate $resumeTemplate, ResumeTemplateCrudService $service): View
    {
        return view('admin.resume-templates.edit', $service->editData($resumeTemplate));
    }

    public function update(
        UpdateResumeTemplateRequest $request,
        ResumeTemplate $resumeTemplate,
        ResumeTemplateCrudService $service,
    ): RedirectResponse {
        $result = $service->update($resumeTemplate, $request->validated(), $request->file('thumbnail'));

        return to_route('admin.templates.edit', $resumeTemplate)->with('success', $result['message']);
    }

    public function preview(
        ResumeTemplate $resumeTemplate,
        TemplateRendererRegistry $renderers,
    ): View {
        abort_unless($renderers->has($resumeTemplate->renderer_key), 404);

        $sample = $renderers->sample($resumeTemplate->renderer_key);

        return view($renderers->view($resumeTemplate->renderer_key), [
            'user' => null,
            'resume' => null,
            'resumeTemplate' => $resumeTemplate,
            'sections' => collect(),
            'data' => $sample,
            'content' => [
                'full_name' => 'Alex Morgan',
                'professional_title' => $sample['title'],
                'email' => 'alex.morgan@example.com',
                'phone' => $sample['phone'],
                'location' => $sample['location'],
                'website' => '',
                'linkedin' => $sample['linkedin'],
                'github' => $sample['github'],
                'social_links' => [],
                'summary' => $sample['summary'],
            ],
            'embedded' => request()->boolean('embedded'),
        ]);
    }

    public function reorder(
        ReorderResumeTemplatesRequest $request,
        ResumeTemplateCrudService $service,
    ): JsonResponse {
        $result = $service->reorder($request->validated('template_ids'));

        return response()->json(['message' => $result['message']]);
    }

    public function destroy(
        ResumeTemplate $resumeTemplate,
        ResumeTemplateCrudService $service,
    ): RedirectResponse {
        $result = $service->delete($resumeTemplate);

        return to_route('admin.templates.index')
            ->with($result['success'] ? 'success' : 'error', $result['message']);
    }
}
