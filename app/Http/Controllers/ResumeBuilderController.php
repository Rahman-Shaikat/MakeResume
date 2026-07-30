<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ReorderResumeSectionItemsRequest;
use App\Http\Requests\ReorderResumeSectionsRequest;
use App\Http\Requests\StoreResumeSectionRequest;
use App\Http\Requests\UpdateResumeContentRequest;
use App\Http\Requests\UpdateResumeSectionRequest;
use App\Http\Requests\UpsertResumeSectionItemRequest;
use App\Http\Resources\ResumeSectionItemResource;
use App\Http\Resources\ResumeSectionResource;
use App\Models\Resume;
use App\Models\ResumeSection;
use App\Models\ResumeSectionItem;
use App\Services\ResumeBuilderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

final class ResumeBuilderController extends Controller
{
    public function __construct(
        private readonly ResumeBuilderService $service,
    ) {}

    public function updateContent(
        UpdateResumeContentRequest $request,
        Resume $resume,
    ): JsonResponse {
        Gate::authorize('update', $resume);
        $resume = $this->service->updateContent($resume, $request->validated());

        return response()->json([
            'message' => 'Resume details saved.',
            'content' => $resume->content,
        ]);
    }

    public function storeSection(
        StoreResumeSectionRequest $request,
        Resume $resume,
    ): JsonResponse {
        Gate::authorize('update', $resume);
        $section = $this->service->storeSection($resume, $request->validated('title'));

        return response()->json([
            'message' => 'Custom section added.',
            'section' => (new ResumeSectionResource($section))->resolve(),
        ], 201);
    }

    public function updateSection(
        UpdateResumeSectionRequest $request,
        Resume $resume,
        ResumeSection $resumeSection,
    ): JsonResponse {
        Gate::authorize('update', $resume);
        Gate::authorize('update', $resumeSection);
        $section = $this->service->updateSection($resume, $resumeSection, $request->validated());

        return response()->json([
            'message' => 'Section updated.',
            'section' => (new ResumeSectionResource($section))->resolve(),
        ]);
    }

    public function destroySection(Resume $resume, ResumeSection $resumeSection): JsonResponse
    {
        Gate::authorize('update', $resume);
        Gate::authorize('delete', $resumeSection);
        $this->service->deleteSection($resume, $resumeSection);

        return response()->json(['message' => 'Custom section deleted.']);
    }

    public function reorderSections(
        ReorderResumeSectionsRequest $request,
        Resume $resume,
    ): JsonResponse {
        Gate::authorize('update', $resume);
        $this->service->reorderSections($resume, $request->validated('section_ids'));

        return response()->json(['message' => 'Section order saved.']);
    }

    public function storeItem(
        UpsertResumeSectionItemRequest $request,
        Resume $resume,
        ResumeSection $resumeSection,
    ): JsonResponse {
        Gate::authorize('update', $resume);
        Gate::authorize('update', $resumeSection);
        $item = $this->service->storeItem($resume, $resumeSection, $request->validated('data'));

        return response()->json([
            'message' => 'Entry added.',
            'item' => (new ResumeSectionItemResource($item))->resolve(),
        ], 201);
    }

    public function updateItem(
        UpsertResumeSectionItemRequest $request,
        Resume $resume,
        ResumeSection $resumeSection,
        ResumeSectionItem $resumeSectionItem,
    ): JsonResponse {
        Gate::authorize('update', $resume);
        Gate::authorize('update', $resumeSection);
        Gate::authorize('update', $resumeSectionItem);
        $item = $this->service->updateItem(
            $resume,
            $resumeSection,
            $resumeSectionItem,
            $request->validated('data'),
        );

        return response()->json([
            'message' => 'Entry saved.',
            'item' => (new ResumeSectionItemResource($item))->resolve(),
        ]);
    }

    public function destroyItem(
        Resume $resume,
        ResumeSection $resumeSection,
        ResumeSectionItem $resumeSectionItem,
    ): JsonResponse {
        Gate::authorize('update', $resume);
        Gate::authorize('update', $resumeSection);
        Gate::authorize('delete', $resumeSectionItem);
        $this->service->deleteItem($resume, $resumeSection, $resumeSectionItem);

        return response()->json(['message' => 'Entry removed.']);
    }

    public function reorderItems(
        ReorderResumeSectionItemsRequest $request,
        Resume $resume,
        ResumeSection $resumeSection,
    ): JsonResponse {
        Gate::authorize('update', $resume);
        Gate::authorize('update', $resumeSection);
        $this->service->reorderItems($resume, $resumeSection, $request->validated('item_ids'));

        return response()->json(['message' => 'Entry order saved.']);
    }
}
