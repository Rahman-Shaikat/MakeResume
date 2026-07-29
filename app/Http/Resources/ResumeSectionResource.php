<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class ResumeSectionResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'key' => $this->section_key,
            'type' => $this->type,
            'title' => $this->title,
            'sort_order' => $this->sort_order,
            'is_custom' => $this->is_custom,
            'is_visible' => $this->is_visible,
            'items' => ResumeSectionItemResource::collection($this->whenLoaded('items')),
        ];
    }
}
