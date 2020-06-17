<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;
use App\Models\RestaurantTable;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use App\Http\Requests\StoreRestaurantTable;

class RestaurantTablesController extends Controller
{
    public function index()
    {
        return Inertia::render('RestaurantTables/Index', [
            'can' => [
                'create' => Auth::user()->can('create restaurant tables'),
                'delete' => Auth::user()->can('delete restaurant tables'),
                'restore' => Auth::user()->can('restore restaurant tables'),
                'view' => Auth::user()->can('view restaurant tables')
            ],
            'filters' => Request::all('search', 'status', 'trashed'),
            'restaurant_tables' => RestaurantTable::with('user')->orderByTableNumber()->filter(Request::only('search', 'status', 'trashed'))->paginate(Request::input('pagination'))
        ]);
    }

    public function create()
    {
        return Inertia::render('RestaurantTables/Create', [
            'users' => User::whereRole('waiter')->get()
        ]);
    }

    public function view(RestaurantTable $table)
    {
        return Inertia::render('RestaurantTables/View', [
            'restaurant_table' => $table->load('user'),
            'can' => [
                'create' => Auth::user()->can('create restaurant tables'),
                'delete' => Auth::user()->can('delete restaurant tables'),
                'restore' => Auth::user()->can('restore restaurant tables'),
                'view' => Auth::user()->can('view restaurant tables')
            ],
            'log' => $table->activities
        ]);
    }

    public function store(StoreRestaurantTable $storeRestaurantTable)
    {
        $alreadyExists = RestaurantTable::where('table_number', Request::input('table_number'))->first();

        if ($alreadyExists) {
          return Redirect::back()->with('error', 'A table with this number already exists.');
        }

        $restaurantTable = RestaurantTable::create([
            'table_number' => Request::input('table_number'),
            'user_id' => Request::input('user_id')
        ]);

        activity()->causedBy(Auth::user())->performedOn($restaurantTable)->log('created');

        return Redirect::route('restaurant_tables')->with('success', 'Table created.');
    }

    public function edit(RestaurantTable $table)
    {
        return Inertia::render('RestaurantTables/Edit', [
            'restaurant_table' => $table,
            'users' => User::whereRole('waiter')->get(),
            'can' => [
                'delete' => Auth::user()->can('delete restaurant tables'),
                'restore' => Auth::user()->can('restore restaurant tables')
            ],
        ]);
    }

    public function update(RestaurantTable $table, StoreRestaurantTable $storeRestaurantTable)
    {
        $alreadyExists = RestaurantTable::where('table_number', Request::input('table_number'))->first();

        if ($alreadyExists && $alreadyExists->id != $table->id) {
          return Redirect::back()->with('error', 'A table with this number already exists.');
        }

        $table->update(Request::only('user_id', 'table_number'));

        activity()->causedBy(Auth::user())->performedOn($table)->log('updated');

        return Redirect::back()->with('success', 'Table updated.');
    }



    public function destroy(RestaurantTable $table)
    {
        $table->delete();

        activity()->causedBy(Auth::user())->performedOn($table)->log('deleted');

        return Redirect::back()->with('success', 'Table deleted.');
    }

    public function restore(RestaurantTable $table)
    {
        $table->restore();

        activity()->causedBy(Auth::user())->performedOn($table)->log('restored');

        return Redirect::back()->with('success', 'Table restored.');
    }
}
