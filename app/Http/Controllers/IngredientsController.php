<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use Inertia\Inertia;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\StoreIngredient;
use Illuminate\Support\Facades\Auth;

class IngredientsController extends Controller
{
    public function index()
    {
        return Inertia::render('Ingredients/Index', [
            'can' => [
                'create' => Auth::user()->can('create ingredients'),
                'edit' => Auth::user()->can('edit ingredients'),
                'view' => Auth::user()->can('view ingredients'),
                'delete' => Auth::user()->can('delete ingredients'),
                'restore' => Auth::user()->can('restore ingredients')
            ],
            'filters' => Request::all('search', 'stock', 'trashed', 'pagination'),
            'ingredients' => Ingredient::orderByName()->filter(Request::only('search', 'stock', 'trashed'))->paginate(Request::input('pagination'))
        ]);
    }

    public function create()
    {
        return Inertia::render('Ingredients/Create');
    }

    public function store(StoreIngredient $request)
    {
        $ingredient = Ingredient::create([
            'name' => Request::input('name'),
            'emergency_quantity' => Request::input('emergency_quantity')
        ]);

        $ingredient->clearStock(Request::input('quantity'));

        activity()->causedBy(Auth::user())->performedOn($ingredient)->log('created');

        return Redirect::route('ingredients')->with('success', 'Ingredient created.');
    }

    public function view(Ingredient $ingredient)
    {
        return Inertia::render('Ingredients/View', [
            'ingredient' => $ingredient,
            'can' => [
                'edit' => Auth::user()->can('edit ingredients'),
                'delete' => Auth::user()->can('delete ingredients'),
                'restore' => Auth::user()->can('restore ingredients')
            ],
            'log' => $ingredient->activities
        ]);
    }

    public function edit(Ingredient $ingredient)
    {
        return Inertia::render('Ingredients/Edit', [
            'ingredient' => $ingredient,
            'can' => [
                'delete' => Auth::user()->can('delete ingredients'),
                'restore' => Auth::user()->can('restore ingredients')
            ]
        ]);
    }

    public function update(Ingredient $ingredient, StoreIngredient $storeIngredient)
    {
        $ingredient->update(Request::only('name', 'emergency_quantity'));

        if(Request::input('quantity')){
            $ingredient->clearStock(Request::input('quantity'));
        }

        activity()->causedBy(Auth::user())->performedOn($ingredient)->log('updated');

        return Redirect::back()->with('success', 'Ingredient updated.');
    }

    public function destroy(Ingredient $ingredient)
    {
        activity()->causedBy(Auth::user())->performedOn($ingredient)->log('deleted');
        
        $ingredient->delete();

        return Redirect::back()->with('success', 'Ingredient deleted.');
    }

    public function restore(Ingredient $ingredient)
    {
        $ingredient->restore();

        activity()->causedBy(Auth::user())->performedOn($ingredient)->log('restored');
        
        return Redirect::back()->with('success', 'Ingredient restored.');
    }
}
