<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Resume;
use App\Models\ResumeSection;
use App\Models\ResumeSectionItem;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class ResumeBuilderService
{
    /**
     * @var array<string, string>
     */
    public const DEFAULT_SECTIONS = [
        'personal' => 'Personal Details',
        'summary' => 'Professional Summary',
        'experience' => 'Experience',
        'education' => 'Education',
        'skills' => 'Skills',
        'projects' => 'Projects',
        'courses' => 'Training / Courses',
        'awards' => 'Awards',
        'languages' => 'Languages',
    ];

    public function load(Resume $resume): Resume
    {
        $this->initializeSections($resume);

        return $resume->load([
            'sections' => fn ($query) => $query
                ->orderBy('sort_order')
                ->with(['items' => fn ($items) => $items->orderBy('sort_order')]),
        ]);
    }

    public function updateContent(Resume $resume, array $content): Resume
    {
        $resume->update([
            'content' => array_replace($resume->content ?? [], $content),
        ]);

        return $resume->fresh();
    }

    public function storeSection(Resume $resume, string $title): ResumeSection
    {
        $resume = $this->load($resume);
        $section = $resume->sections()->create([
            'section_key' => (string) Str::uuid(),
            'type' => 'custom',
            'title' => $title,
            'sort_order' => ((int) $resume->sections->max('sort_order')) + 1,
            'is_custom' => true,
            'is_visible' => true,
        ]);
        $section->setRelation('items', collect());

        return $section;
    }

    public function updateSection(Resume $resume, ResumeSection $section, array $data): ResumeSection
    {
        $this->ensureSectionBelongsToResume($resume, $section);
        $section->update([
            ...($section->is_custom && array_key_exists('title', $data)
                ? ['title' => $data['title']]
                : []),
            ...(array_key_exists('is_visible', $data)
                ? ['is_visible' => $data['is_visible']]
                : []),
        ]);

        return $section->load('items');
    }

    public function deleteSection(Resume $resume, ResumeSection $section): void
    {
        $this->ensureSectionBelongsToResume($resume, $section);
        $section->delete();
    }

    /** @param array<int, int|string> $sectionIds */
    public function reorderSections(Resume $resume, array $sectionIds): void
    {
        $resume = $this->load($resume);
        $requestedIds = collect($sectionIds)->map(fn ($id): int => (int) $id);
        $ownedIds = $resume->sections->pluck('id');

        abort_unless($requestedIds->sort()->values()->all() === $ownedIds->sort()->values()->all(), 422);

        DB::transaction(function () use ($requestedIds, $resume): void {
            $sections = $resume->sections->keyBy('id');
            $requestedIds->each(function (int $id, int $index) use ($sections): void {
                $sections->get($id)?->update(['sort_order' => $index]);
            });
        });
    }

    public function storeItem(Resume $resume, ResumeSection $section, array $data): ResumeSectionItem
    {
        $this->ensureSectionBelongsToResume($resume, $section);

        return $section->items()->create([
            'data' => $data,
            'sort_order' => ((int) $section->items()->max('sort_order')) + 1,
        ]);
    }

    public function updateItem(
        Resume $resume,
        ResumeSection $section,
        ResumeSectionItem $item,
        array $data,
    ): ResumeSectionItem {
        $this->ensureItemBelongsToSection($resume, $section, $item);
        $item->update(['data' => $data]);

        return $item->refresh();
    }

    public function deleteItem(
        Resume $resume,
        ResumeSection $section,
        ResumeSectionItem $item,
    ): void {
        $this->ensureItemBelongsToSection($resume, $section, $item);
        $item->delete();
    }

    /** @param array<int, int|string> $itemIds */
    public function reorderItems(Resume $resume, ResumeSection $section, array $itemIds): void
    {
        $this->ensureSectionBelongsToResume($resume, $section);
        $requestedIds = collect($itemIds)->map(fn ($id): int => (int) $id);
        $items = $section->items()->get()->keyBy('id');

        abort_unless(
            $requestedIds->sort()->values()->all() === $items->keys()->sort()->values()->all(),
            422,
        );

        DB::transaction(function () use ($requestedIds, $items): void {
            $requestedIds->each(function (int $id, int $index) use ($items): void {
                $items->get($id)?->update(['sort_order' => $index]);
            });
        });
    }

    private function initializeSections(Resume $resume): void
    {
        $timestamp = Carbon::now();
        $rows = [];

        foreach (self::DEFAULT_SECTIONS as $key => $title) {
            $rows[] = [
                'resume_id' => $resume->id,
                'section_key' => $key,
                'type' => $key,
                'title' => $title,
                'sort_order' => array_search($key, array_keys(self::DEFAULT_SECTIONS), true),
                'is_custom' => false,
                'is_visible' => true,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];
        }

        ResumeSection::query()->insertOrIgnore($rows);
    }

    private function ensureSectionBelongsToResume(Resume $resume, ResumeSection $section): void
    {
        abort_unless($section->resume_id === $resume->id, 404);
    }

    private function ensureItemBelongsToSection(
        Resume $resume,
        ResumeSection $section,
        ResumeSectionItem $item,
    ): void {
        $this->ensureSectionBelongsToResume($resume, $section);
        abort_unless($item->resume_section_id === $section->id, 404);
    }
}
