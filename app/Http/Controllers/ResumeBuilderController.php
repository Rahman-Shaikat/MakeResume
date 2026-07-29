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
use App\Models\ResumeSection;
use App\Models\ResumeSectionItem;
use App\Services\ResumeBuilderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

final class ResumeBuilderController extends Controller
{
    public function __construct(
        private readonly ResumeBuilderService $builderService,
    ) {}

    public function updateContent(UpdateResumeContentRequest $request): JsonResponse
    {
        $resume = $request->user()->resume;
        $resume->update([
            'content' => array_replace($resume->content ?? [], $request->validated()),
        ]);

        return response()->json([
            'message' => 'Resume details saved.',
            'content' => $resume->fresh()->content,
        ]);
    }

    public function storeSection(StoreResumeSectionRequest $request): JsonResponse
    {
        $resume = $this->builderService->load($request->user()->resume);
        $section = $resume->sections()->create([
            'section_key' => (string) Str::uuid(),
            'type' => 'custom',
            'title' => $request->validated('title'),
            'sort_order' => ((int) $resume->sections->max('sort_order')) + 1,
            'is_custom' => true,
            'is_visible' => true,
        ]);
        $section->setRelation('items', collect());

        return response()->json([
            'message' => 'Custom section added.',
            'section' => (new ResumeSectionResource($section))->resolve(),
        ], 201);
    }

    public function updateSection(
        UpdateResumeSectionRequest $request,
        ResumeSection $resumeSection,
    ): JsonResponse {
        Gate::authorize('update', $resumeSection);
        $validated = $request->validated();

        $resumeSection->update([
            ...($resumeSection->is_custom && array_key_exists('title', $validated)
                ? ['title' => $validated['title']]
                : []),
            ...(array_key_exists('is_visible', $validated)
                ? ['is_visible' => $validated['is_visible']]
                : []),
        ]);

        return response()->json([
            'message' => 'Section updated.',
            'section' => (new ResumeSectionResource($resumeSection->load('items')))->resolve(),
        ]);
    }

    public function destroySection(ResumeSection $resumeSection): JsonResponse
    {
        Gate::authorize('delete', $resumeSection);
        $resumeSection->delete();

        return response()->json(['message' => 'Custom section deleted.']);
    }

    public function reorderSections(ReorderResumeSectionsRequest $request): JsonResponse
    {
        $resume = $this->builderService->load($request->user()->resume);
        $requestedIds = collect($request->validated('section_ids'))->map(fn ($id) => (int) $id);
        $ownedIds = $resume->sections->pluck('id');

        abort_unless($requestedIds->sort()->values()->all() === $ownedIds->sort()->values()->all(), 422);

        DB::transaction(function () use ($requestedIds, $resume): void {
            $sections = $resume->sections->keyBy('id');
            $requestedIds->each(function (int $id, int $index) use ($sections): void {
                $sections->get($id)->update(['sort_order' => $index]);
            });
        });

        return response()->json(['message' => 'Section order saved.']);
    }

    public function storeItem(
        UpsertResumeSectionItemRequest $request,
        ResumeSection $resumeSection,
    ): JsonResponse {
        Gate::authorize('update', $resumeSection);
        $item = $resumeSection->items()->create([
            'data' => $request->validated('data'),
            'sort_order' => ((int) $resumeSection->items()->max('sort_order')) + 1,
        ]);

        return response()->json([
            'message' => 'Entry added.',
            'item' => (new ResumeSectionItemResource($item))->resolve(),
        ], 201);
    }

    public function updateItem(
        UpsertResumeSectionItemRequest $request,
        ResumeSection $resumeSection,
        ResumeSectionItem $resumeSectionItem,
    ): JsonResponse {
        Gate::authorize('update', $resumeSection);
        Gate::authorize('update', $resumeSectionItem);
        abort_unless($resumeSectionItem->resume_section_id === $resumeSection->id, 404);

        $resumeSectionItem->update(['data' => $request->validated('data')]);

        return response()->json([
            'message' => 'Entry saved.',
            'item' => (new ResumeSectionItemResource($resumeSectionItem))->resolve(),
        ]);
    }

    public function destroyItem(
        ResumeSection $resumeSection,
        ResumeSectionItem $resumeSectionItem,
    ): JsonResponse {
        Gate::authorize('update', $resumeSection);
        Gate::authorize('delete', $resumeSectionItem);
        abort_unless($resumeSectionItem->resume_section_id === $resumeSection->id, 404);

        $resumeSectionItem->delete();

        return response()->json(['message' => 'Entry removed.']);
    }

    public function reorderItems(
        ReorderResumeSectionItemsRequest $request,
        ResumeSection $resumeSection,
    ): JsonResponse {
        Gate::authorize('update', $resumeSection);
        $requestedIds = collect($request->validated('item_ids'))->map(fn ($id) => (int) $id);
        $items = $resumeSection->items()->get()->keyBy('id');

        abort_unless(
            $requestedIds->sort()->values()->all() === $items->keys()->sort()->values()->all(),
            422,
        );

        DB::transaction(function () use ($requestedIds, $items): void {
            $requestedIds->each(function (int $id, int $index) use ($items): void {
                $items->get($id)->update(['sort_order' => $index]);
            });
        });

        return response()->json(['message' => 'Entry order saved.']);
    }
}
