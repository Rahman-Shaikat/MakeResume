<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

final class ReorderResumeTemplatesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'template_ids' => ['required', 'array', 'min:1'],
            'template_ids.*' => ['required', 'integer', 'distinct', 'exists:resume_templates,id'],
        ];
    }
}
