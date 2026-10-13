<?php

use App\Support\SectionPermissions;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        foreach (array_keys(SectionPermissions::ALL) as $name) {
            DB::table('permissions')->insertOrIgnore([
                'name' => $name,
                'guard_name' => 'web',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $permissions = DB::table('permissions')->pluck('id', 'name');

        foreach (SectionPermissions::LEGACY_MAP as $legacyName => $replacementNames) {
            $legacyId = $permissions->get($legacyName);

            if (! $legacyId) {
                continue;
            }

            $roleIds = DB::table('role_has_permissions')
                ->where('permission_id', $legacyId)
                ->pluck('role_id');
            $modelAssignments = DB::table('model_has_permissions')
                ->where('permission_id', $legacyId)
                ->get(['model_type', 'model_id']);

            foreach ($replacementNames as $replacementName) {
                $replacementId = $permissions->get($replacementName);

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

        $obsoletePermissions = array_diff(
            array_keys(SectionPermissions::LEGACY_MAP),
            array_keys(SectionPermissions::ALL),
        );

        DB::table('permissions')->whereIn('name', $obsoletePermissions)->delete();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
