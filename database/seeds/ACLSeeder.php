<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\User;
use Spatie\Permission\PermissionRegistrar;

class ACLSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Users
        Permission::create(['name' => 'delete users']);
        Permission::create(['name' => 'create users']);
        Permission::create(['name' => 'restore users']);
        Permission::create(['name' => 'edit users']);
        Permission::create(['name' => 'view users']);

        // Orders
        Permission::create(['name' => 'delete orders']);
        Permission::create(['name' => 'create orders']);
        Permission::create(['name' => 'restore orders']);
        Permission::create(['name' => 'edit orders']);
        Permission::create(['name' => 'view orders']);

        // Ingredients
        Permission::create(['name' => 'create ingredients']);
        Permission::create(['name' => 'delete ingredients']);
        Permission::create(['name' => 'restore ingredients']);
        Permission::create(['name' => 'edit ingredients']);
        Permission::create(['name' => 'view ingredients']);

        // Recipes
        Permission::create(['name' => 'create recipes']);
        Permission::create(['name' => 'delete recipes']);
        Permission::create(['name' => 'restore recipes']);
        Permission::create(['name' => 'edit recipes']);
        Permission::create(['name' => 'view recipes']);

        // Restaurant Tables
        Permission::create(['name' => 'create restaurant tables']);
        Permission::create(['name' => 'delete restaurant tables']);
        Permission::create(['name' => 'restore restaurant tables']);
        Permission::create(['name' => 'edit restaurant tables']);
        Permission::create(['name' => 'view restaurant tables']);

        // Roles
        Permission::create(['name' => 'create roles']);
        Permission::create(['name' => 'delete roles']);
        Permission::create(['name' => 'edit roles']);
        Permission::create(['name' => 'view roles']);

        // Create roles and assign existing permissions
        $role1 = Role::create(['name' => 'Waiter']);
        $role1->givePermissionTo('edit orders');
        $role1->givePermissionTo('create orders');

        $role2 = Role::create(['name' => 'Manager']);

        $role2->givePermissionTo('edit users');
        $role2->givePermissionTo('create users');
        $role2->givePermissionTo('delete users');
        $role2->givePermissionTo('restore users');
        $role2->givePermissionTo('view users');

        $role2->givePermissionTo('edit orders');
        $role2->givePermissionTo('create orders');
        $role2->givePermissionTo('delete orders');
        $role2->givePermissionTo('restore orders');
        $role2->givePermissionTo('view orders');

        $role2->givePermissionTo('create ingredients');
        $role2->givePermissionTo('delete ingredients');
        $role2->givePermissionTo('restore ingredients');
        $role2->givePermissionTo('edit ingredients');
        $role2->givePermissionTo('view ingredients');

        $role2->givePermissionTo('create recipes');
        $role2->givePermissionTo('delete recipes');
        $role2->givePermissionTo('restore recipes');
        $role2->givePermissionTo('edit recipes');
        $role2->givePermissionTo('view recipes');

        $role2->givePermissionTo('create restaurant tables');
        $role2->givePermissionTo('delete restaurant tables');
        $role2->givePermissionTo('restore restaurant tables');
        $role2->givePermissionTo('view restaurant tables');

        $role2->givePermissionTo('create roles');
        $role2->givePermissionTo('delete roles');
        $role2->givePermissionTo('edit roles');
        $role2->givePermissionTo('view roles');

        $role3 = Role::create(['name' => 'Administrator']);

        $user = factory(User::class)->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'johndoe@example.com',
            'status' => 1
        ]);
        $user->assignRole($role2);

        $users = factory(User::class, 50)->create();

        $users->each(function($user) use ($role1, $role2, $role3){
            $role_name = 'role'.rand(1,3);
            $user->assignRole($$role_name);
        });

    }
}
