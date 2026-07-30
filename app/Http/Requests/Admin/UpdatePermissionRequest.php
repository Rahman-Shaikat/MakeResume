<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Models\Permission;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdatePermissionRequest extends FormRequest
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
            'short_desc' => ['nullable', 'string', 'max:255'],
            'type' => ['required', Rule::in([1])],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            /** @var Permission $permission */
            $permission = $this->route('permission');
            $parentId = (int) $this->input('parent_id');

            if ($parentId === $permission->id) {
                $validator->errors()->add('parent_id', 'A permission cannot be its own parent.');

                return;
            }

            if ($parentId > 0) {
                $parent = Permission::query()->find($parentId);

                if ($parent && $parent->group_id !== (int) $this->input('group_id')) {
                    $validator->errors()->add('parent_id', 'The parent permission must belong to the selected group.');
                }
            }

            if ($parentId > 0 && $permission->children()->where('status', 1)->exists()) {
                $validator->errors()->add('parent_id', 'A parent permission with active children cannot become a child permission.');
            }
        });
    }
}
