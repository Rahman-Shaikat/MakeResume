<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ReorderCategoriesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_ids' => ['required', 'array', 'min:1'],
            'category_ids.*' => ['required', 'integer', 'distinct', 'exists:categories,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'category_ids.required' => 'A complete category order is required.',
            'category_ids.*.distinct' => 'Each category may appear only once in the order.',
            'category_ids.*.exists' => 'The category list changed. Refresh the page and try again.',
        ];
    }
}
