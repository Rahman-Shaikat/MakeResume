<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;
use Illuminate\Validation\Validator;

class StoreHomepageHeroRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return $this->rulesFor(null);
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $this->validatePreviewImage($validator);

            if ((int) $this->input('status') === 1 && ! $this->input('resume_template_id') && ! $this->hasFile('preview_image')) {
                $validator->errors()->add('resume_template_id', 'Choose a template or upload a preview image before publishing this hero.');
            }
        });
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['status' => $this->input('status', 2)]);
    }

    /** @return array<string, array<int, mixed>> */
    protected function rulesFor(?int $ignoreId): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'resume_template_id' => ['nullable', 'integer', Rule::exists('resume_templates', 'id')->where('status', 1)],
            'preview_image' => ['nullable', File::image()->types(['jpg', 'jpeg', 'png', 'webp'])->max(2048), 'extensions:jpg,jpeg,png,webp'],
            'remove_preview_image' => ['nullable', 'boolean'],
            'eyebrow' => ['required', 'string', 'max:120'],
            'headline' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:2000'],
            'top_badge' => ['required', 'string', 'max:160'],
            'editor_title' => ['required', 'string', 'max:160'],
            'editor_description' => ['required', 'string', 'max:255'],
            'bottom_status_title' => ['required', 'string', 'max:160'],
            'bottom_status_text' => ['required', 'string', 'max:160'],
            'status' => ['required', Rule::in([1, 2])],
        ];
    }

    protected function validatePreviewImage(Validator $validator): void
    {
        $file = $this->file('preview_image');

        if (! $file instanceof UploadedFile || ! $file->isValid()) {
            return;
        }

        $dimensions = @getimagesize($file->getPathname());

        if ($dimensions === false) {
            $validator->errors()->add('preview_image', 'The preview must be a genuine image.');

            return;
        }

        [$width, $height] = $dimensions;
        $ratio = $height > 0 ? $width / $height : 0;

        if ($width < 700 || $height < 990) {
            $validator->errors()->add('preview_image', 'The preview must be at least 700 × 990 pixels.');
        }

        if ($height <= $width || abs($ratio - (210 / 297)) > 0.035) {
            $validator->errors()->add('preview_image', 'The preview must use a portrait A4 aspect ratio.');
        }
    }
}
