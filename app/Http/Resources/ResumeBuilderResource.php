<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

final class ResumeBuilderResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'template_slug' => $this->template_slug,
            'profile_image_url' => $this->profile_image
                ? Storage::disk('public')->url($this->profile_image)
                : null,
            'content' => $this->content ?? [],
            'sections' => ResumeSectionResource::collection($this->whenLoaded('sections')),
            'preview_url' => route('resume.preview', [
                'resume' => $this->id,
                'embed' => 1,
                'builder' => 1,
            ]),
        ];
    }
}
