<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'parent_id' => (int) $this->input('parent_id') === 0
                ? ['required', 'integer', 'in:0']
                : [
                    'required',
                    'integer',
                    Rule::exists('categories', 'id')->where(fn ($query) => $query
                        ->where('parent_id', 0)
                        ->where('status', 1)),
                ],
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                'unique:categories,slug',
            ],
            'short_desc' => ['nullable', 'string', 'max:2000'],
            'status' => ['required', Rule::in([1, 2])],
            'is_featured' => ['required', Rule::in([1, 2])],
        ];
    }

    protected function prepareForValidation(): void
    {
        $slugSource = trim((string) $this->input('slug'));

        $this->merge([
            'slug' => Str::slug($slugSource !== '' ? $slugSource : (string) $this->input('name')),
            'parent_id' => $this->input('parent_id', 0),
            'status' => $this->input('status', 1),
            'is_featured' => $this->input('is_featured', 2),
        ]);
    }
}
