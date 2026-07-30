<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreHomepageTemplateShowcaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'eyebrow' => ['required', 'string', 'max:120'],
            'headline' => ['required', 'string', 'max:255'],
            'cta_label' => ['required', 'string', 'max:120'],
            'status' => ['required', Rule::in([1, 2])],
            'template_ids' => ['nullable', 'array', 'max:4'],
            'template_ids.*' => ['required', 'integer', 'distinct', Rule::exists('resume_templates', 'id')->where('status', 1)],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ((int) $this->input('status') === 1 && count($this->input('template_ids', [])) !== 4) {
                $validator->errors()->add('template_ids', 'An active template showcase must contain exactly four active templates.');
            }
        });
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'status' => $this->input('status', 2),
            'template_ids' => $this->input('template_ids', []),
        ]);
    }
}
