<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Models\AdminUser;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAdminUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var AdminUser $adminUser */
        $adminUser = $this->route('adminUser');

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
            'email' => [
                'required',
                'email',
                'max:100',
                Rule::unique('admin_users', 'email')->ignore($adminUser),
            ],
            'gender' => ['required', Rule::in([0, 1, 2, 3])],
            'phone' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('admin_users', 'phone')->ignore($adminUser),
            ],
            'address' => ['nullable', 'string', 'max:255'],
            'image_path' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'status' => ['required', Rule::in([1, 2])],
            'is_super' => ['required', Rule::in([1, 2])],
        ];
    }
}
