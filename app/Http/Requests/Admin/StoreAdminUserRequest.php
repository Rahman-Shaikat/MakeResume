<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAdminUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'role_id' => [
                'required',
                'integer',
                Rule::exists('roles', 'id')->where(fn ($query) => $query
                    ->where('type', 1)
                    ->where('status', 1)),
            ],
            'country_id' => ['required', 'integer', 'min:0'],
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100', 'unique:admin_users,email'],
            'gender' => ['required', Rule::in([0, 1, 2, 3])],
            'phone' => ['nullable', 'string', 'max:20', 'unique:admin_users,phone'],
            'address' => ['nullable', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'max:50', 'confirmed'],
            'image_path' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_super' => ['nullable', Rule::in([1, 2])],
        ];
    }
}
