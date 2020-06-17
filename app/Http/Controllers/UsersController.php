<?php

namespace App\Http\Controllers;

use App\User;
use Inertia\Inertia;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\StoreUser;
use App\Http\Requests\UpdateUser;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class UsersController extends Controller
{
    public function index()
    {
        return Inertia::render('Users/Index', [
            'can' => [
                'create' => Auth::user()->can('create users'),
                'edit' => Auth::user()->can('edit users'),
                'view' => Auth::user()->can('view users'),
                'delete' => Auth::user()->can('delete users'),
                'restore' => Auth::user()->can('restore users')
            ],
            'roles' => Role::select('name')->get(),
            'filters' => Request::all('search', 'role', 'status', 'trashed', 'pagination'),
            'users' => User::orderByName()->filter(Request::only('search', 'role', 'status', 'trashed'))->paginate(Request::input('pagination'))
        ]);
    }

    public function create()
    {
        return Inertia::render('Users/Create', [
          'roles' => Role::select('name')->get(),
        ]);
    }

    public function store(StoreUser $request)
    {
        if (Request::input('role') === 'select') {
          return Redirect::back()->with('error', 'A role must be selected.');
        }

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => $request->password
        ]);

        $user->syncRoles([Request::input('role')]);
        
        activity()->causedBy(Auth::user())->performedOn($user)->log('created');

        return Redirect::route('users')->with('success', 'User created.');
    }

    public function view(User $user)
    {
        return Inertia::render('Users/View', [
            'user' => $user,
            'can' => [
                'edit' => Auth::user()->can('edit users'),
                'delete' => Auth::user()->can('delete users'),
                'restore' => Auth::user()->can('restore users')
            ],
            'log' => $user->activities
        ]);
    }

    public function edit(User $user)
    {
        return Inertia::render('Users/Edit', [
            'user' => $user,
            'roles' => Role::select('name')->get(),
            'can' => [
                'delete' => Auth::user()->can('delete users'),
                'restore' => Auth::user()->can('restore users'),
                'edit_roles' => Auth::user()->can('edit roles')
            ],
        ]);
    }

    public function update(User $user, UpdateUser $userRequest)
    {
        if(Request::input('password') != null){
            $user->update(Request::only('first_name', 'last_name', 'email', 'password', 'status'));
        } else {
            $user->update(Request::only('first_name', 'last_name', 'email', 'status'));
        }

        if(Request::input('role')){
            $user->syncRoles([Request::input('role')]);
        }

        activity()->causedBy(Auth::user())->performedOn($user)->log('updated');

        return Redirect::back()->with('success', 'User updated.');
    }

    public function destroy(User $user)
    {
        $user->delete();

        activity()->causedBy(Auth::user())->performedOn($user)->log('deleted');

        return Redirect::back()->with('success', 'User deleted.');
    }

    public function restore(User $user)
    {
        $user->restore();

        activity()->causedBy(Auth::user())->performedOn($user)->log('restored');

        return Redirect::back()->with('success', 'User restored.');
    }
}
