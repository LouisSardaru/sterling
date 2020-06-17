<?php

use Faker\Generator as Faker;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\Models\OrdersRecipe::class, function (Faker $faker) {
    return [
        'order_id' => rand(1, 50),
        'recipe_id' => rand(1, 50),
        'quantity' => 1,
        'price' => rand (1, 500)
    ];
});
