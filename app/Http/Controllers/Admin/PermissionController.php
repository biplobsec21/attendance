<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index()
    {
        // Group permissions by module (based on route prefix)
        $permissions = Permission::all()->groupBy(function ($perm) {
            $parts = explode('.', $perm->name);
            return $parts[0] ?? 'Other';
        });

        $roles = Role::all();

        return view('mpm.page.admin.permissions.index', compact('permissions', 'roles'));
    }

    public function create()
    {
        return view('admin.permissions.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:permissions,name'
        ]);

        Permission::create(['name' => $validated['name']]);

        return redirect()->route('permissions.index')->with('success', 'Permission created');
    }

    public function edit(Permission $permission)
    {
        return view('admin.permissions.edit', compact('permission'));
    }

    // public function update(Request $request, Permission $permission)
    // {
    //     $validated = $request->validate([
    //         'name' => "required|unique:permissions,name,{$permission->id}"
    //     ]);

    //     $permission->update(['name' => $validated['name']]);

    //     return redirect()->route('permissions.index')->with('success', 'Permission updated');
    // }
    public function update(Request $request, Role $role)
    {
        $role->syncPermissions($request->permissions ?? []);
        return redirect()->back()->with('success', 'Permissions updated successfully!');
    }
    public function destroy(Permission $permission)
    {
        $permission->delete();
        return redirect()->route('permissions.index')->with('success', 'Permission deleted');
    }
}
