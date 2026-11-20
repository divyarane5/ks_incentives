<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\Modules;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/** KeyPeople > Module access: which roles (and which extra people) can open each module. */
class ModuleAccessController extends Controller
{
    private function authorizeAdmin(): void
    {
        abort_unless(isSuperAdmin() || isAdmin(), 403, 'Only Admin or Superadmin can change module access.');
    }

    public function index(Request $request)
    {
        $this->authorizeAdmin();

        $roles = Role::with('permissions:id,name')->withCount('users')->orderBy('name')->get();
        $extras = User::whereHas('permissions', fn ($q) => $q->where('name', 'like', 'module-%'))
            ->with(['permissions:id,name', 'roles:id,name'])->orderBy('name')->get();
        $people = User::active()->orderBy('name')->get(['id', 'name', 'employee_code']);

        return view('module_access.index', [
            'modules' => Modules::ALL,
            'roles' => $roles,
            'extras' => $extras,
            'people' => $people,
        ]);
    }

    public function updateRoles(Request $request)
    {
        $this->authorizeAdmin();
        $grid = (array) $request->input('access', []);

        foreach (Role::all() as $role) {
            if ($role->name === config('roles.SUPER_ADMIN')) {
                continue; // always has everything
            }
            foreach (array_keys(Modules::ALL) as $key) {
                $perm = Permission::findOrCreate(Modules::permission($key), 'web');
                $want = ! empty($grid[$role->id][$key]);
                if ($want && ! $role->hasPermissionTo($perm)) {
                    $role->givePermissionTo($perm);
                } elseif (! $want && $role->hasPermissionTo($perm)) {
                    $role->revokePermissionTo($perm);
                }
            }
        }
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('module-access.index')->with('success', 'Module access by role saved.');
    }

    public function updateUser(Request $request)
    {
        $this->authorizeAdmin();
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'modules' => ['array'],
            'modules.*' => ['in:'.implode(',', array_keys(Modules::ALL))],
        ]);

        $user = User::findOrFail($data['user_id']);
        $keep = $user->getDirectPermissions()->reject(fn ($p) => str_starts_with($p->name, 'module-'))->pluck('name')->all();
        $extra = array_map(fn ($k) => Modules::permission($k), $data['modules'] ?? []);
        foreach ($extra as $name) {
            Permission::findOrCreate($name, 'web');
        }
        $user->syncPermissions(array_merge($keep, $extra));
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return redirect()->route('module-access.index')->with('success', 'Extra modules for '.$user->name.' saved.');
    }
}
