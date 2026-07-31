<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class StoreContactMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['bail', 'required', 'string', 'max:120'],
            'email' => ['bail', 'required', 'string', 'email:rfc', 'max:255'],
            'topic' => ['bail', 'required', 'string', Rule::in(['account', 'feedback', 'partnership', 'other'])],
            'message' => ['bail', 'required', 'string', 'min:20', 'max:5000'],
            'website' => ['nullable', 'string', 'max:0'],
        ];
    }
}
