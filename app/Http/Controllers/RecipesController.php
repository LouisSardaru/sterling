<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use Inertia\Inertia;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\StoreRecipe;
use App\Models\IngredientsRecipe;
use App\Models\Recipe;
use Illuminate\Support\Facades\Auth;

class RecipesController extends Controller
{
    public function index()
    {
        return Inertia::render('Recipes/Index', [
            'can' => [
                'create' => Auth::user()->can('create recipes'),
                'edit' => Auth::user()->can('edit recipes'),
                'view' => Auth::user()->can('view recipes'),
                'delete' => Auth::user()->can('delete recipes'),
                'restore' => Auth::user()->can('restore recipes')
            ],
            'filters' => Request::all('search', 'trashed', 'pagination'),
            'recipes' => Recipe::orderByName()->filter(Request::only('search', 'trashed'))->paginate(Request::input('pagination')),
        ]);
    }

    public function create()
    {
        return Inertia::render('Recipes/Create');
    }

    public function view(Recipe $recipe)
    {
        return Inertia::render('Recipes/View', [
            'recipe' => $recipe->load('ingredients_recipe.ingredient'),
            'can' => [
                'edit' => Auth::user()->can('edit recipes'),
                'delete' => Auth::user()->can('delete recipes'),
                'restore' => Auth::user()->can('restore recipes')
            ],
            'log' => $recipe->activities
        ]);
    }


    public function store(StoreRecipe $request)
    {
        $recipe = Recipe::create([
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
            'calories' => $request->calories,
            'dietary_restrictions' => $request->dietary_restrictions
        ]);

        if($request->file('product_image')){
            $recipe->addMediaFromRequest('product_image')->preservingOriginal()->toMediaCollection('product');
        }

        activity()->causedBy(Auth::user())->performedOn($recipe)->log('created');

        return Redirect::route('recipes')->with('success', 'Recipe created.');
    }

    public function addIngredient(Recipe $recipe, Ingredient $ingredient, Request $request){
        if (!is_numeric(Request::input('quantity')) || Request::input('quantity') < 1) {
            return Redirect::back()->with('error', 'A valid quantity must be entered.');
        }

        $ingredientRecipe = IngredientsRecipe::create(['recipe_id' => $recipe->id, 'ingredient_id' => $ingredient->id, 'quantity' => Request::input('quantity')]);

        activity()->causedBy(Auth::user())->performedOn($recipe)->log('added ingredient "'.$ingredient->name.'" to recipe');

        return Redirect::back()->with('success', 'Ingredient added.');
    }

    public function updateIngredient(IngredientsRecipe $ingredientRecipe, Request $request){
        if (!is_numeric(Request::input('quantity')) || Request::input('quantity') < 1) {
            return Redirect::back()->with('error', 'A valid quantity must be entered.');
        }

        $ingredientRecipe->update(Request::only('quantity'));

        activity()->causedBy(Auth::user())->performedOn($ingredientRecipe)->log('updated quantity');

        return Redirect::back()->with('success', 'Ingredient quantity updated.');
    }

    public function removeIngredient(IngredientsRecipe $ingredientRecipe){
        $ingredientRecipe->delete();

        activity()->causedBy(Auth::user())->performedOn($ingredientRecipe)->log('removed ingredient "'.$ingredientRecipe->ingredient->name.'" from recipe');

        return Redirect::back()->with('success', 'Ingredient deleted.');
    }


    public function edit(Recipe $recipe)
    {
        if (Request::has('ingredients') && Request::input('ingredients') === 'added') {
          $ingredients = Ingredient::whereAddedToRecipe($recipe->id)->orderByName()->filter(Request::only('search'))->paginate(Request::input('pagination'));
        } else {
          $ingredients = Ingredient::orderByName()->filter(Request::only('search'))->paginate(Request::input('pagination'));
        }

        return Inertia::render('Recipes/Edit', [
            'recipe' => $recipe,
            'filters' => Request::all('search', 'pagination'),
            'added_ingredients' => $recipe->ingredients_recipe,
            'ingredients' => $ingredients,
            'can' => [
                'delete' => Auth::user()->can('delete recipes'),
                'restore' => Auth::user()->can('restore recipes')
            ],
        ]);
    }

    public function update(Recipe $recipe, StoreRecipe $request)
    {
        $recipe->update(Request::only('name', 'price', 'description', 'calories', 'dietary_restrictions'));

        if($request->file('product_image')){
            $recipe->addMediaFromRequest('product_image')->preservingOriginal()->toMediaCollection('product');
        }

        activity()->causedBy(Auth::user())->performedOn($recipe)->log('updated');

        return Redirect::back()->with('success', 'Recipe updated.');
    }

    public function destroy(Recipe $recipe)
    {
        $recipe->delete();

        activity()->causedBy(Auth::user())->performedOn($recipe)->log('deleted');

        return Redirect::back()->with('success', 'Recipe deleted.');
    }

    public function restore(Recipe $recipe)
    {
        $recipe->restore();

        activity()->causedBy(Auth::user())->performedOn($recipe)->log('restored');

        return Redirect::back()->with('success', 'Recipe restored.');
    }
}
