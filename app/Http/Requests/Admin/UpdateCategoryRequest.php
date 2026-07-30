<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var Category $category */
        $category = $this->route('category');

        return [
            'parent_id' => (int) $this->input('parent_id') === 0
                ? ['required', 'integer', 'in:0']
                : [
                    'required',
                    'integer',
                    Rule::exists('categories', 'id')->where(fn ($query) => $query
                        ->where('parent_id', 0)
                        ->where(function ($query) use ($category): void {
                            $query->where('status', 1)
                                ->when(
                                    $category->parent_id > 0,
                                    fn ($query) => $query->orWhere('id', $category->parent_id),
                                );
                        })),
                ],
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('categories', 'slug')->ignore($category),
            ],
            'short_desc' => ['nullable', 'string', 'max:2000'],
            'status' => ['required', Rule::in([1, 2])],
            'is_featured' => ['required', Rule::in([1, 2])],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            /** @var Category $category */
            $category = $this->route('category');
            $parentId = (int) $this->input('parent_id');

            if ($parentId === $category->id) {
                $validator->errors()->add('parent_id', 'A category cannot be its own parent.');

                return;
            }

            if ($parentId > 0 && (int) $this->input('status') === 1) {
                $parent = Category::query()->find($parentId);

                if ($parent && $parent->status !== 1) {
                    $validator->errors()->add('parent_id', 'An active subcategory requires an active parent category.');
                }
            }

            if ($parentId > 0 && $category->children()->where('status', 1)->exists()) {
                $validator->errors()->add('parent_id', 'A category with active subcategories cannot become a subcategory.');
            }
        });
    }

    protected function prepareForValidation(): void
    {
        $slugSource = trim((string) $this->input('slug'));

        $this->merge([
            'slug' => Str::slug($slugSource !== '' ? $slugSource : (string) $this->input('name')),
            'parent_id' => $this->input('parent_id', 0),
        ]);
    }
}
