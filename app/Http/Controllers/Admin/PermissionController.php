<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    /**
     * Display grouped permissions with roles.
     */
    public function index()
    {
        // Flat list for CRUD table
        $permissionsFlat = Permission::orderBy('name')->get();

        // Grouped list for role-assignment UI (module.action -> grouped by module)
        $permissions = $permissionsFlat->groupBy(function ($perm) {
            $parts = explode('.', $perm->name);
            return $parts[0] ?? 'Other';
        });

        $roles = Role::orderBy('name')->get();

        return view('mpm.page.admin.permissions.index', compact('permissions', 'permissionsFlat', 'roles'));
    }

    /**
     * Show form for creating a permission.
     */
    public function create()
    {
        return view('mpm.page.admin.permissions.create');
    }

    /**
     * Store a new permission.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:permissions,name'
        ]);

        $permission = Permission::create(['name' => $validated['name']]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'permission' => $permission,
                'message' => 'Permission created successfully!',
            ]);
        }

        return redirect()->route('permissions.index')->with('success', 'Permission created');
    }

    /**
     * Show form for editing a permission.
     */
    public function edit(Permission $permission)
    {
        return view('mpm.page.admin.permissions.edit', compact('permission'));
    }

    /**
     * Update a permission name.
     */
    public function update(Request $request, Permission $permission)
    {
        $validated = $request->validate([
            'name' => "required|unique:permissions,name,{$permission->id}"
        ]);

        $permission->update(['name' => $validated['name']]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'permission' => $permission,
                'message' => 'Permission updated successfully!',
            ]);
        }

        return redirect()->route('permissions.index')->with('success', 'Permission updated');
    }

    /**
     * Sync permissions for a role.
     */
    public function updateRolePermissions(Request $request, Role $role)
    {
        $role->syncPermissions($request->permissions ?? []);

        return response()->json([
            'success' => true,
            'message' => 'Permissions updated successfully!',
        ]);
    }

    /**
     * Get permissions of a role (AJAX).
     */
    public function getRolePermissions(Role $role)
    {
        return response()->json([
            'permissions' => $role->permissions->pluck('name'),
        ]);
    }

    /**
     * Delete a permission.
     */
    public function destroy(Request $request, Permission $permission)
    {
        $permission->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Permission deleted successfully!',
            ]);
        }

        return redirect()->route('permissions.index')->with('success', 'Permission deleted');
    }
}
