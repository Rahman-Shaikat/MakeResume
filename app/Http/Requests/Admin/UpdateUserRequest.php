<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Enums\ResumeLimitMode;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

final class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        /** @var User $user */
        $user = $this->route('user');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user),
            ],
            'verification_status' => ['required', 'integer', Rule::in([1, 2])],
            'resume_limit_mode' => ['required', Rule::enum(ResumeLimitMode::class)],
            'resume_limit' => [
                'nullable',
                'integer',
                'min:0',
                Rule::requiredIf($this->input('resume_limit_mode') === ResumeLimitMode::Limited->value),
            ],
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
