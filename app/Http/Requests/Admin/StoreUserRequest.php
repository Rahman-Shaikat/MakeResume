<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Enums\ResumeLimitMode;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

final class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')],
            'verification_status' => ['required', 'integer', Rule::in([1, 2])],
            'resume_limit_mode' => ['required', Rule::enum(ResumeLimitMode::class)],
            'resume_limit' => [
                'nullable',
                'integer',
                'min:0',
                Rule::requiredIf($this->input('resume_limit_mode') === ResumeLimitMode::Limited->value),
            ],
            'password' => ['required', 'string', 'min:8', 'max:255', 'confirmed'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $data = [
            'name' => trim((string) $this->input('name')),
            'email' => Str::lower(trim((string) $this->input('email'))),
        ];

        if ($this->input('resume_limit_mode') !== ResumeLimitMode::Limited->value) {
            $data['resume_limit'] = null;
        }

        $this->merge($data);
    }
}
