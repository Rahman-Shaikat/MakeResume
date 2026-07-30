<?php

declare(strict_types=1);

namespace App\Services\Admin;

use App\Models\HomepageTemplateShowcase;
use App\Models\ResumeTemplate;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

final class HomepageTemplateShowcaseCrudService
{
    public function indexData(): array
    {
        return [
            'showcases' => HomepageTemplateShowcase::query()
                ->with(['templates:id,name'])
                ->latest('updated_at')
                ->get(),
        ];
    }

    public function createData(): array
    {
        return [
            'resumeTemplates' => $this->resumeTemplates(),
            'statusOptions' => $this->statusOptions(),
        ];
    }

    public function editData(HomepageTemplateShowcase $showcase): array
    {
        $showcase->load('templates:id,name');

        return [
            'homepageTemplateShowcase' => $showcase,
            'resumeTemplates' => $this->resumeTemplates(),
            'statusOptions' => $this->statusOptions(),
        ];
    }

    public function store(array $data): array
    {
        $showcase = DB::transaction(function () use ($data): HomepageTemplateShowcase {
            $templateIds = $this->templateIds($data);
            unset($data['template_ids']);

            if ((int) $data['status'] === 1) {
                HomepageTemplateShowcase::query()->active()->lockForUpdate()->update(['status' => 2]);
            }

            $showcase = HomepageTemplateShowcase::query()->create($data);
            $showcase->templates()->sync($this->templateSyncData($templateIds));

            return $showcase;
        });

        return ['success' => true, 'message' => 'Homepage template showcase created successfully.', 'model' => $showcase];
    }

    public function update(HomepageTemplateShowcase $showcase, array $data): array
    {
        DB::transaction(function () use ($showcase, $data): void {
            $templateIds = $this->templateIds($data);
            unset($data['template_ids']);

            if ((int) $data['status'] === 1) {
                HomepageTemplateShowcase::query()
                    ->active()
                    ->where('id', '!=', $showcase->id)
                    ->lockForUpdate()
                    ->update(['status' => 2]);
            }

            $lockedShowcase = HomepageTemplateShowcase::query()->lockForUpdate()->findOrFail($showcase->id);
            $lockedShowcase->update($data);
            $lockedShowcase->templates()->sync($this->templateSyncData($templateIds));
        });

        return ['success' => true, 'message' => 'Homepage template showcase updated successfully.', 'model' => $showcase->refresh()];
    }

    public function delete(HomepageTemplateShowcase $showcase): array
    {
        $showcase->delete();

        return ['success' => true, 'message' => 'Homepage template showcase deleted successfully.'];
    }

    /** @return Collection<int, ResumeTemplate> */
    private function resumeTemplates(): Collection
    {
        return ResumeTemplate::query()
            ->where('status', 1)
            ->orderByRaw('CASE WHEN is_featured = 1 THEN 0 ELSE 1 END')
            ->orderBy('position')
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    /** @param array<string, mixed> $data
     * @return list<int>
     */
    private function templateIds(array $data): array
    {
        return collect($data['template_ids'] ?? [])
            ->map(fn ($templateId): int => (int) $templateId)
            ->values()
            ->all();
    }

    /** @param list<int> $templateIds
     * @return array<int, array{position: int}>
     */
    private function templateSyncData(array $templateIds): array
    {
        return collect($templateIds)
            ->mapWithKeys(fn (int $templateId, int $position): array => [$templateId => ['position' => $position]])
            ->all();
    }

    /** @return list<array{value: int, label: string, description: string}> */
    private function statusOptions(): array
    {
        return [
            ['value' => 1, 'label' => 'Active', 'description' => 'Publish this showcase on the public homepage'],
            ['value' => 2, 'label' => 'Inactive', 'description' => 'Keep this configuration as a draft'],
        ];
    }
}
