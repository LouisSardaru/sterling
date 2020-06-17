<?php
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Auth
Route::get('login')->name('login')->uses('Auth\LoginController@showLoginForm')->middleware('guest');
Route::post('login')->name('login.attempt')->uses('Auth\LoginController@login')->middleware('guest');
Route::post('logout')->name('logout')->uses('Auth\LoginController@logout');
Route::get('logout')->name('logout')->uses('Auth\LoginController@logout');

// Dashboard
Route::get('/')->name('dashboard')->uses('DashboardController')->middleware('auth');

// Users
Route::get('users')->name('users')->uses('UsersController@index')->middleware('remember', 'auth');
Route::get('users/{user}/view')->name('users.view')->uses('UsersController@view')->middleware('auth');
Route::get('users/create')->name('users.create')->uses('UsersController@create')->middleware('auth');
Route::post('users')->name('users.store')->uses('UsersController@store')->middleware('auth');
Route::get('users/{user}/edit')->name('users.edit')->uses('UsersController@edit')->middleware('auth');
Route::put('users/{user}')->name('users.update')->uses('UsersController@update')->middleware('auth');
Route::delete('users/{user}')->name('users.destroy')->uses('UsersController@destroy')->middleware('auth');
Route::put('users/{user}/restore')->name('users.restore')->uses('UsersController@restore')->middleware('auth');

// Ingredients
Route::get('ingredients')->name('ingredients')->uses('IngredientsController@index')->middleware('remember', 'auth');
Route::get('ingredients/{ingredient}/view')->name('ingredients.view')->uses('IngredientsController@view')->middleware('auth');
Route::get('ingredients/create')->name('ingredients.create')->uses('IngredientsController@create')->middleware('auth');
Route::post('ingredients')->name('ingredients.store')->uses('IngredientsController@store')->middleware('auth');
Route::get('ingredients/{ingredient}/edit')->name('ingredients.edit')->uses('IngredientsController@edit')->middleware('auth');
Route::put('ingredients/{ingredient}')->name('ingredients.update')->uses('IngredientsController@update')->middleware('auth');
Route::delete('ingredients/{ingredient}')->name('ingredients.destroy')->uses('IngredientsController@destroy')->middleware('auth');
Route::put('ingredients/{ingredient}/restore')->name('ingredients.restore')->uses('IngredientsController@restore')->middleware('auth');

// Restaurant tables
Route::get('tables')->name('restaurant_tables')->uses('RestaurantTablesController@index')->middleware('remember', 'auth');
Route::get('tables/{restaurant_table}/view')->name('restaurant_tables.view')->uses('RestaurantTablesController@view')->middleware('auth');
Route::get('tables/create')->name('restaurant_tables.create')->uses('RestaurantTablesController@create')->middleware('auth');
Route::post('tables')->name('restaurant_tables.store')->uses('RestaurantTablesController@store')->middleware('auth');
Route::get('tables/{restaurant_table}/edit')->name('restaurant_tables.edit')->uses('RestaurantTablesController@edit')->middleware('auth');
Route::put('tables/{restaurant_table}')->name('restaurant_tables.update')->uses('RestaurantTablesController@update')->middleware('auth');
Route::delete('tables/{restaurant_table}')->name('restaurant_tables.destroy')->uses('RestaurantTablesController@destroy')->middleware('auth');
Route::put('tables/{restaurant_table}/restore')->name('restaurant_tables.restore')->uses('RestaurantTablesController@restore')->middleware('auth');

// Recipe
Route::get('recipes')->name('recipes')->uses('RecipesController@index')->middleware('remember', 'auth');
Route::get('recipes/{recipe}/view')->name('recipes.view')->uses('RecipesController@view')->middleware('auth');
Route::get('recipes/create')->name('recipes.create')->uses('RecipesController@create')->middleware('auth');
Route::get('recipes/{recipe}/edit')->name('recipes.edit')->uses('RecipesController@edit')->middleware('auth');
Route::put('recipes/{recipe}')->name('recipes.update')->uses('RecipesController@update')->middleware('auth');
Route::post('recipes')->name('recipes.store')->uses('RecipesController@store')->middleware('auth');
Route::delete('recipes/{recipe}')->name('recipes.destroy')->uses('RecipesController@destroy')->middleware('auth');
Route::put('recipes/{recipe}/restore')->name('recipes.restore')->uses('RecipesController@restore')->middleware('auth');

// Ingredient <-> Recipe
Route::post('recipes/{recipe}/{ingredient}/add')->name('recipes.add_ingredient')->uses('RecipesController@addIngredient')->middleware('auth');
Route::post('recipes/{ingredient_recipe}/update')->name('recipes.update_ingredient')->uses('RecipesController@updateIngredient')->middleware('auth');
Route::delete('recipes/{ingredient_recipe}/remove')->name('recipes.remove_ingredient')->uses('RecipesController@removeIngredient')->middleware('auth');

// Orders
Route::get('orders')->name('orders')->uses('OrdersController@index')->middleware('remember', 'auth');
Route::get('orders/{order}/view')->name('orders.view')->uses('OrdersController@view')->middleware('auth');
Route::get('orders/create')->name('orders.create')->uses('OrdersController@create')->middleware('auth');
Route::post('orders')->name('orders.store')->uses('OrdersController@store')->middleware('auth');
Route::get('orders/{order}/edit')->name('orders.edit')->uses('OrdersController@edit')->middleware('auth');
Route::put('orders/{order}')->name('orders.update')->uses('OrdersController@update')->middleware('auth');
Route::delete('orders/{order}')->name('orders.destroy')->uses('OrdersController@destroy')->middleware('auth');
Route::put('orders/{order}/restore')->name('orders.restore')->uses('OrdersController@restore')->middleware('auth');

// Order Items
Route::post('orders/{order}/{recipe}/add')->name('orders.add_recipe')->uses('OrdersController@addRecipe')->middleware('auth');
Route::post('orders/{order_recipe}/update')->name('orders.update_recipe')->uses('OrdersController@updateRecipe')->middleware('auth');
Route::delete('orders/{order_recipe}/remove')->name('orders.remove_recipe')->uses('OrdersController@removeRecipe')->middleware('auth');

// Roles
Route::get('roles')->name('roles')->uses('RolesController@index')->middleware('remember', 'auth');
Route::get('roles/{role}/view')->name('roles.view')->uses('RolesController@view')->middleware('auth');
Route::get('roles/create')->name('roles.create')->uses('RolesController@create')->middleware('auth');
Route::post('roles')->name('roles.store')->uses('RolesController@store')->middleware('auth');
Route::get('roles/{role}/edit')->name('roles.edit')->uses('RolesController@edit')->middleware('auth');
Route::put('roles/{role}')->name('roles.update')->uses('RolesController@update')->middleware('auth');
Route::delete('roles/{role}')->name('roles.destroy')->uses('RolesController@destroy')->middleware('auth');

// 500 error
Route::get('500', function () {
    echo $fail;
});
