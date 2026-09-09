<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Role::class, 'role');
    }

    public function index(): View
    {
        return view('admin.roles.index', [
            'roles' => Role::withCount('users')->orderBy('name')->get(),
            'breadcrumbs' => ['Dashboard' => route('admin.dashboard'), 'Users & Roles' => route('admin.users.index'), 'Roles' => null],
        ]);
    }

    public function create(): View
    {
        return view('admin.roles.create', ['permissionGroups' => $this->groupedPermissions()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:roles,name'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,name'],
        ]);

        $role = Role::create(['name' => $data['name'], 'guard_name' => 'web']);
        $role->syncPermissions($data['permissions'] ?? []);

        ActivityLog::record('created', description: "Role \"{$role->name}\" created with " . count($data['permissions'] ?? []) . ' permissions');

        return redirect()->route('admin.roles.index')->with('success', "Role \"{$role->name}\" created.");
    }

    public function edit(Role $role): View
    {
        return view('admin.roles.edit', [
            'role' => $role,
            'permissionGroups' => $this->groupedPermissions(),
            'rolePermissions' => $role->permissions->pluck('name')->toArray(),
        ]);
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $data = $request->validate([
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,name'],
        ]);

        $role->syncPermissions($data['permissions'] ?? []);

        ActivityLog::record('updated', description: "Role \"{$role->name}\" permissions updated");

        return redirect()->route('admin.roles.index')->with('success', "Role \"{$role->name}\" updated.");
    }

    public function destroy(Role $role): RedirectResponse
    {
        if ($role->users()->exists()) {
            return back()->with('error', "Can't delete \"{$role->name}\" — users are still assigned to it.");
        }

        $name = $role->name;
        $role->delete();

        ActivityLog::record('deleted', description: "Role \"{$name}\" deleted");

        return redirect()->route('admin.roles.index')->with('success', "Role \"{$name}\" deleted.");
    }

    /**
     * Group permissions by their module prefix (e.g. "customers.view" -> "customers")
     * so the edit screen can render one checkbox row per module instead of a flat list.
     */
    protected function groupedPermissions()
    {
        return Permission::orderBy('name')->get()->groupBy(function ($permission) {
            return explode('.', $permission->name)[0];
        });
    }
}
