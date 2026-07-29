<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class ResumeBuilderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->resume !== null;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:100'],
            'professional_title' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:40'],
            'location' => ['required', 'string', 'max:160'],
            'linkedin' => ['nullable', 'url:http,https', 'max:255'],
            'github' => ['nullable', 'url:http,https', 'max:255'],
            'summary' => ['required', 'string', 'max:600'],
        ];
    }
}
