<?php

namespace Database\Seeders;

use App\Enums\UserTypeEnum;
use App\Models\User;
use App\Support\SectionPermissions;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = collect(array_keys(SectionPermissions::ALL))->mapWithKeys(
            fn (string $name) => [$name => Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web'])]
        );

        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $teacher = Role::firstOrCreate(['name' => 'Teacher', 'guard_name' => 'web']);
        $user = Role::firstOrCreate(['name' => 'User', 'guard_name' => 'web']);

        $superAdmin->syncPermissions($permissions->values());
        $teacher->syncPermissions($permissions->only(SectionPermissions::TEACHER)->values());
        $user->syncPermissions($permissions->only(SectionPermissions::USER)->values());

        User::query()->each(function (User $account) use ($superAdmin, $teacher, $user) {
            if ($account->roles()->exists()) {
                return;
            }

            $role = match ((int) $account->user_type_id) {
                UserTypeEnum::SuperAdmin->value => $superAdmin,
                UserTypeEnum::Teacher->value => $teacher,
                default => $user,
            };

            $account->assignRole($role);
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
