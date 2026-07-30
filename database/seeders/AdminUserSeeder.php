<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\AdminUser;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $role = Role::query()
            ->where('name', 'Super Admin')
            ->where('type', 1)
            ->firstOrFail();

        AdminUser::query()->firstOrCreate(
            ['email' => env('ADMIN_EMAIL', 'shaikat_super@admin.com')],
            [
                'role_id' => $role->id,
                'country_id' => 0,
                'name' => 'Super Admin',
                'phone' => null,
                'gender' => 0,
                'password' => Hash::make(env('ADMIN_PASSWORD', 'password')),
                'status' => 1,
                'is_super' => 1,
                'email_verified_at' => now(),
            ],
        );
    }
}
