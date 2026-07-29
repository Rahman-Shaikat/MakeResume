<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Resume;
use App\Models\ResumeSection;
use Illuminate\Support\Carbon;

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
}
