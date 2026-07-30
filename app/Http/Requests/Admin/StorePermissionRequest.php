<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Models\Permission;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StorePermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'group_id' => [
                'required',
                'integer',
                Rule::exists('permission_groups', 'id')->where(fn ($query) => $query
                    ->where('type', 1)
                    ->where('status', 1)),
            ],
            'parent_id' => (int) $this->input('parent_id') === 0
                ? ['required', 'integer', 'in:0']
                : [
                    'required',
                    'integer',
                    Rule::exists('permissions', 'id')->where(fn ($query) => $query
                        ->where('parent_id', 0)
                        ->where('type', 1)
                        ->where('status', 1)),
                ],
            'name' => ['required', 'string', 'max:100'],
            'meta_name' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                'unique:permissions,meta_name',
            ],
            'short_desc' => ['nullable', 'string', 'max:255'],
            'type' => ['required', Rule::in([1])],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $parentId = (int) $this->input('parent_id');

            if ($parentId === 0) {
                return;
            }

            $parent = Permission::query()->find($parentId);

            if ($parent && $parent->group_id !== (int) $this->input('group_id')) {
                $validator->errors()->add('parent_id', 'The parent permission must belong to the selected group.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'meta_name.regex' => 'The permission key must use lowercase kebab-case, for example users-update.',
        ];
    }
}
