<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\StoreIngredient;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use App\Models\OrdersRecipe;
use App\Models\Recipe;
use App\Models\RestaurantTable;
use App\Models\IngredientsRecipe;
use App\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class OrdersController extends Controller
{
    public function index()
    {
        return Inertia::render('Orders/Index', [
            'can' => [
                'create' => Auth::user()->can('create orders'),
                'edit' => Auth::user()->can('edit orders'),
                'view' => Auth::user()->can('view orders'),
                'delete' => Auth::user()->can('delete orders'),
                'restore' => Auth::user()->can('restore orders')
            ],
            'filters' => Request::all('search', 'type', 'status', 'restaurant_table', 'trashed'),
            'restaurant_tables' => RestaurantTable::whereAssigned()->get(),
            'orders' => Order::with('restaurantTable.user')->orderDescByDate()->filter(Request::only('search', 'type', 'status', 'restaurant_table', 'trashed'))->paginate(Request::input('pagination'))
        ]);
    }

    public function create()
    {
        return Inertia::render('Orders/Create',[
            'recipes' => Recipe::all(),
            'restaurant_tables' => RestaurantTable::whereAssigned()->get(),
        ]);
    }

    public function store(Request $request)
    {
        if (Request::input('is_dine_in') && !Request::input('restaurant_table_id')) {
            return Redirect::back()->with('error', 'A table must be selected.');
        }

        if (Request::input('is_dine_in') && Request::input('restaurant_table_id')) {
            $order = Order::create([
                'status' => Order::STATUS_UNASSIGNED,
                'restaurant_table_id' => Request::input('restaurant_table_id')
            ]);
        } else {
            $order = Order::create([
                'status' => Order::STATUS_UNASSIGNED
            ]);
        }

        activity()->causedBy(Auth::user())->performedOn($order)->log('created');

        return Redirect::route('orders')->with('success', 'Order created.');
    }

    public function view(Order $order)
    {
        return Inertia::render('Orders/View', [
            'order' => $order->load(['restaurantTable.user', 'order_recipes.recipe']),
            'can' => [
                'delete' => Auth::user()->can('delete orders'),
                'restore' => Auth::user()->can('restore orders'),
                'edit' => Auth::user()->can('edit orders')
            ],
            'log' => $order->activities
        ]);
    }

    public function edit(Order $order)
    {
        if($order->status==Order::STATUS_DELIVERED){
            return Redirect::route('orders')->with('error', 'Cannot edit a delivered order.');
        }

        if (Request::has('recipes') && Request::input('recipes') === 'added') {
          $recipes = Recipe::whereAddedToOrder($order->id)->orderByName()->filter(Request::only('search'))->paginate(Request::input('pagination'));
        } else {
          $recipes = Recipe::orderByName()->filter(Request::only('search'))->paginate(Request::input('pagination'));
        }

        return Inertia::render('Orders/Edit', [
            'order' => $order,
            'can' => [
                'delete' => Auth::user()->can('delete orders'),
                'restore' => Auth::user()->can('restore orders')
            ],
            'filters' => Request::all('search', 'pagination'),
            'added_recipes' => $order->order_recipes,
            'recipes' => $recipes,
            'restaurant_tables' => RestaurantTable::whereAssigned()->get(),
        ]);
    }

    public function update(Order $order)
    {
        if (Request::input('is_dine_in') && !Request::input('restaurant_table_id')) {
            return Redirect::back()->with('error', 'A table must be selected');
        }

        if (Request::input('is_dine_in') && Request::input('restaurant_table_id')) {
          $order->update(Request::only('restaurant_table_id', 'status'));
        } else {
          $order->update(Request::only('status'));
          $order->restaurant_table_id = null;
          $order->save();
        } 

        activity()->causedBy(Auth::user())->performedOn($order)->log('updated');

        if ((int)Request::input('status') === Order::STATUS_DELIVERED) {
          return Redirect::route('orders')->with('success', 'Order updated.');
        }

        return Redirect::back()->with('success', 'Order updated.');
    }

    public function destroy(Order $order)
    {
        activity()->causedBy(Auth::user())->performedOn($order)->log('deleted');
        $order->delete();

        return Redirect::back()->with('success', 'Order deleted.');
    }

    public function restore(Order $order)
    {
        $order->restore();
        activity()->causedBy(Auth::user())->performedOn($order)->log('restored');
        return Redirect::back()->with('success', 'Order restored.');
    }

    public function addRecipe(Order $order, Recipe $recipe, Request $request) {
        if (!is_numeric(Request::input('quantity')) || Request::input('quantity') < 1) {
            return Redirect::back()->with('error', 'A valid quantity must be entered.');
        }

        foreach($recipe->ingredients_recipe as $ingredientRecipe) {
            if ($ingredientRecipe->ingredient->quantity < $ingredientRecipe->quantity * Request::input('quantity')) {
                return Redirect::back()->with('error', 'Not enough ingredients to prepare this recipe.');
            }
        }

        $ordersRecipe = OrdersRecipe::create([
            'order_id' => $order->id,
            'recipe_id' => $recipe->id,
            'price' => $recipe->price,
            'quantity' => Request::input('quantity')
        ]);

        foreach($recipe->ingredients_recipe as $ingredientRecipe) {
            $ingredientRecipe->ingredient->decreaseStock($ingredientRecipe->quantity * Request::input('quantity'));
        }

        activity()->causedBy(Auth::user())->performedOn($order)->log('added "'.$recipe->name.'" to order');

        return Redirect::back()->with('success', 'Order item created.');
    }

    public function updateRecipe(OrdersRecipe $ordersRecipe, Request $request){
        if (!is_numeric(Request::input('quantity')) || Request::input('quantity') < 1) {
            return Redirect::back()->with('error', 'A valid quantity must be entered.');
        }

        foreach($ordersRecipe->recipe->ingredients_recipe as $ingredientRecipe) {
            if ($ingredientRecipe->ingredient->quantity < $ingredientRecipe->quantity * Request::input('quantity')) {
                return Redirect::back()->with('error', 'Not enough ingredients to prepare this recipe.');
            }
        }

        $ordersRecipe->update(Request::only('quantity'));

        foreach($ordersRecipe->recipe->ingredients_recipe as $ingredientRecipe) {
            $ingredientRecipe->ingredient->decreaseStock($ingredientRecipe->quantity * Request::input('quantity'));
        }

        activity()->causedBy(Auth::user())->performedOn($ordersRecipe)->log('updated quantity');
        
        return Redirect::back()->with('success', 'Order quantity updated.');
    }

    public function removeRecipe(OrdersRecipe $ordersRecipe) {
        foreach($ordersRecipe->recipe->ingredients_recipe as $ingredientRecipe) {
            $ingredientRecipe->ingredient->increaseStock($ingredientRecipe->quantity * $ordersRecipe->quantity);
        }

        $ordersRecipe->delete();

        activity()->causedBy(Auth::user())->performedOn($ordersRecipe)->log('removed "'.$ordersRecipe->recipe->name.'" from order');

        return Redirect::back()->with('success', 'Order item deleted.');
    }

}
