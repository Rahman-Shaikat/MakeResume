<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class SelectResumeTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'template_slug' => [
                'required',
                'string',
                'in:'.implode(',', array_keys(config('resume_templates.catalog'))),
            ],
        ];
    }
}
