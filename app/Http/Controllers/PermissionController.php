<?php

namespace App\Http\Controllers;

use App\Support\SectionPermissions;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index()
    {
        return inertia('Permission/Index', [
            'permissions' => Permission::withCount('roles')->orderBy('name')->get(),
            'sectionPermissions' => array_keys(SectionPermissions::ALL),
        ]);
    }

    public function create()
    {
        return inertia('Permission/Create');
    }

    public function store(Request $request)
    {
        $validated = $this->validatePermission($request);
        Permission::create(['name' => $validated['name'], 'guard_name' => 'web']);

        return redirect()->route('permissions.index')->with('success', 'Permission created successfully.');
    }

    public function edit(Permission $permission)
    {
        return inertia('Permission/Edit', [
            'permission' => $permission,
            'isProtected' => array_key_exists($permission->name, SectionPermissions::ALL),
        ]);
    }

    public function update(Request $request, Permission $permission)
    {
        abort_if(array_key_exists($permission->name, SectionPermissions::ALL), 422, 'Section permissions cannot be renamed.');
        $validated = $this->validatePermission($request, $permission);
        $permission->update(['name' => $validated['name']]);

        return redirect()->route('permissions.index')->with('success', 'Permission updated successfully.');
    }

    public function destroy(Permission $permission)
    {
        abort_if(array_key_exists($permission->name, SectionPermissions::ALL), 422, 'Section permissions cannot be deleted.');
        $permission->delete();

        return redirect()->route('permissions.index')->with('success', 'Permission deleted successfully.');
    }

    private function validatePermission(Request $request, ?Permission $permission = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-z0-9._-]+$/', Rule::unique('permissions', 'name')->ignore($permission?->id)],
        ]);
    }
}
