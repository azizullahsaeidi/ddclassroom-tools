<?php

namespace Database\Seeders;

use App\Enums\UserTypeEnum;
use App\Models\User;
use App\Support\SectionPermissions;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $previousPermissions = [
            'students.manage' => ['students.view', 'students.create', 'students.edit', 'students.delete'],
            'attendance.manage' => ['attendance.view', 'attendance.create', 'attendance.edit', 'attendance.delete'],
            'reports.view' => ['reports.view', 'reports.export'],
            'scores.manage' => ['scores.view', 'scores.create', 'scores.edit', 'scores.delete'],
            'shoqa.view' => ['shoqa.view', 'shoqa.export'],
            'countries.manage' => ['countries.view', 'countries.create', 'countries.edit', 'countries.delete'],
            'grades.manage' => ['grades.view', 'grades.create', 'grades.edit', 'grades.delete'],
            'subjects.manage' => ['subjects.view', 'subjects.create', 'subjects.edit', 'subjects.delete'],
            'semester-subjects.manage' => ['semester-subjects.view', 'semester-subjects.create', 'semester-subjects.edit', 'semester-subjects.delete'],
            'years.manage' => ['years.view', 'years.create', 'years.edit', 'years.delete'],
            'results.manage' => ['results.view', 'results.create', 'results.edit', 'results.delete'],
            'homeroom-teachers.manage' => ['homeroom-teachers.view', 'homeroom-teachers.create', 'homeroom-teachers.edit', 'homeroom-teachers.delete'],
            'skipped-subjects.manage' => ['skipped-subjects.view', 'skipped-subjects.create', 'skipped-subjects.edit', 'skipped-subjects.delete'],
            'users.manage' => ['users.view', 'users.create', 'users.edit', 'users.delete'],
            'roles.manage' => ['roles.view', 'roles.create', 'roles.edit', 'roles.delete'],
            'permissions.manage' => ['permissions.view', 'permissions.create', 'permissions.edit', 'permissions.delete'],
            'backups.manage' => ['backups.view', 'backups.download'],
        ];

        $permissions = collect(array_keys(SectionPermissions::ALL))->mapWithKeys(
            fn (string $name) => [$name => Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web'])]
        );

        $storedPermissions = Permission::pluck('id', 'name');

        foreach ($previousPermissions as $previousName => $replacementNames) {
            $previousId = $storedPermissions->get($previousName);

            if (! $previousId) {
                continue;
            }

            $roleIds = DB::table('role_has_permissions')->where('permission_id', $previousId)->pluck('role_id');
            $modelAssignments = DB::table('model_has_permissions')
                ->where('permission_id', $previousId)
                ->get(['model_type', 'model_id']);

            foreach ($replacementNames as $replacementName) {
                $replacementId = $permissions[$replacementName]->id;

                foreach ($roleIds as $roleId) {
                    DB::table('role_has_permissions')->insertOrIgnore([
                        'permission_id' => $replacementId,
                        'role_id' => $roleId,
                    ]);
                }

                foreach ($modelAssignments as $assignment) {
                    DB::table('model_has_permissions')->insertOrIgnore([
                        'permission_id' => $replacementId,
                        'model_type' => $assignment->model_type,
                        'model_id' => $assignment->model_id,
                    ]);
                }
            }
        }

        Permission::whereIn('name', array_diff(
            array_keys($previousPermissions),
            array_keys(SectionPermissions::ALL),
        ))->delete();

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
