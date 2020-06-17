<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Http\Requests\StoreRole;

class RolesController extends Controller
{
    public function index()
    {
        return Inertia::render('Roles/Index', [
            'can' => [
                'create' => Auth::user()->can('create roles'),
                'edit' => Auth::user()->can('edit roles'),
                'view' => Auth::user()->can('view roles'),
                'delete' => Auth::user()->can('delete roles')
            ],
            'filters' => Request::all('pagination'),
            'roles' => Role::paginate(Request::input('pagination'))
        ]);
    }

    public function create()
    {
        return Inertia::render('Roles/Create');
    }

    public function store(StoreRole $request)
    {
        $role = Role::create([
          'name' => Request::input('name')
        ]);

        activity()->causedBy(Auth::user())->performedOn($role)->log('created');

        return Redirect::route('roles')->with('success', 'Role created.');
    }

    public function view(Role $role)
    {
        return Inertia::render('Roles/View', [
            'permissions' => $permissions,
            'role' => $role,
            'can' => [
                'edit' => Auth::user()->can('edit roles'),
                'delete' => Auth::user()->can('delete roles')
            ],
            'log' => $role->activities
        ]);
    }

    public function edit(Role $role)
    {
        $permissions = collect();
        foreach(Permission::all() as $permission) {
            $permissions->push([
                'name' => $permission->name,
                'status' => $role->hasPermissionTo($permission) ? true : false
            ]);
        }

        return Inertia::render('Roles/Edit', [
            'permissions' => $permissions,
            'role' => $role,
            'can' => [
                'delete' => Auth::user()->can('delete roles')
            ],
        ]);
    }

    public function update(Role $role, StoreRole $request)
    {
        $role->update(Request::only('name'));

        foreach(json_decode(Request::input('permissions')) as $permission){
            $permission->status == true ? $role->givePermissionTo($permission->name) : $role->revokePermissionTo($permission->name);
        }

        activity()->causedBy(Auth::user())->performedOn($role)->log('updated');

        return Redirect::back()->with('success', 'Role updated.');
    }

    public function destroy(Role $role)
    {
        $userWithRole = User::whereRole($role->name)->first();

        if ($userWithRole) {
          return Redirect::back()->with('error', 'There are still users with this role.');
        }

        $role->delete();

        activity()->causedBy(Auth::user())->performedOn($role)->log('deleted');

        return Redirect::route('roles')->with('success', 'Role deleted.');
    }

}
