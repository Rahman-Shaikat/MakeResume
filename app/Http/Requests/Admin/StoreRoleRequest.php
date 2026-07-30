<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('roles')->where(fn ($query) => $query->where('status', 1)),
            ],
            'short_desc' => ['nullable', 'string', 'max:255'],
            'type' => ['required', Rule::in([1])],
            'permission' => ['nullable', 'array'],
            'permission.*' => [
                'integer',
                Rule::exists('permissions', 'id')->where(fn ($query) => $query
                    ->where('type', 1)
                    ->where('status', 1)),
            ],
        ];
    }
}
