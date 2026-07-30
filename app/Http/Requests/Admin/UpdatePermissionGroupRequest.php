<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePermissionGroupRequest extends FormRequest
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
                Rule::unique('permission_groups')
                    ->ignore($this->route('permissionGroup'))
                    ->where(fn ($query) => $query
                        ->where('type', 1)
                        ->where('status', 1)),
            ],
            'short_desc' => ['nullable', 'string', 'max:255'],
            'type' => ['required', Rule::in([1])],
        ];
    }
}
