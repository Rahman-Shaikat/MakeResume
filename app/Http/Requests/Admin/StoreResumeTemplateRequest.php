<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Services\TemplateRendererRegistry;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;
use Illuminate\Validation\Validator;

final class StoreResumeTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                'unique:resume_templates,slug',
            ],
            'renderer_key' => ['required', Rule::in(array_keys(config('resume_templates.renderers', [])))],
            'short_desc' => ['nullable', 'string', 'max:2000'],
            'thumbnail' => ['nullable', File::image()->types(['jpg', 'jpeg', 'png', 'webp'])->max(2048), 'extensions:jpg,jpeg,png,webp'],
            'accent_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'allows_profile_photo' => ['required', Rule::in([1, 2])],
            'is_ats_friendly' => ['required', Rule::in([1, 2])],
            'is_featured' => ['required', Rule::in([1, 2])],
            'status' => ['required', Rule::in([1, 2])],
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['integer', 'distinct', Rule::exists('categories', 'id')->where('status', 1)],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $this->validateImageDimensions($validator);

            if ((int) $this->input('status') === 1 && ! $this->hasFile('thumbnail')) {
                $validator->errors()->add('thumbnail', 'An active template requires a thumbnail.');
            }

            $renderer = app(TemplateRendererRegistry::class);

            if (
                (int) $this->input('allows_profile_photo') === 1
                && $renderer->has((string) $this->input('renderer_key'))
                && ! $renderer->get((string) $this->input('renderer_key'))['supports_profile_photo']
            ) {
                $validator->errors()->add('allows_profile_photo', 'The selected renderer does not support profile photos.');
            }
        });
    }

    protected function prepareForValidation(): void
    {
        $slug = trim((string) $this->input('slug'));

        $this->merge([
            'slug' => Str::slug($slug !== '' ? $slug : (string) $this->input('name')),
            'accent_color' => strtoupper((string) $this->input('accent_color', '#00B6CE')),
            'status' => $this->input('status', 2),
            'is_featured' => $this->input('is_featured', 2),
            'is_ats_friendly' => $this->input('is_ats_friendly', 1),
            'allows_profile_photo' => $this->input('allows_profile_photo', 1),
            'category_ids' => $this->input('category_ids', []),
        ]);
    }

    private function validateImageDimensions(Validator $validator): void
    {
        $file = $this->file('thumbnail');

        if (! $file instanceof UploadedFile || ! $file->isValid()) {
            return;
        }

        $dimensions = @getimagesize($file->getPathname());

        if ($dimensions === false) {
            $validator->errors()->add('thumbnail', 'The thumbnail must be a genuine image.');

            return;
        }

        [$width, $height] = $dimensions;
        $ratio = $height > 0 ? $width / $height : 0;
        $a4Ratio = 210 / 297;

        if ($width < 700 || $height < 990) {
            $validator->errors()->add('thumbnail', 'The thumbnail must be at least 700 × 990 pixels.');
        }

        if ($height <= $width || abs($ratio - $a4Ratio) > 0.035) {
            $validator->errors()->add('thumbnail', 'The thumbnail must use a portrait A4 aspect ratio.');
        }
    }
}
