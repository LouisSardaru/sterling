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

$factory->define(App\Models\Ingredient::class, function (Faker $faker) {
    $faker->addProvider(new \FakerRestaurant\Provider\en_US\Restaurant($faker));
    $random = rand(1,6);
    switch($random){
        case 1:
            $name = $faker->beverageName();
            break;
        case 2:
            $name = $faker->dairyName();
            break;
        case 3:
            $name = $faker->vegetableName();
            break;
        case 4:
            $name = $faker->fruitName();
            break;
        case 5:
            $name = $faker->meatName();
            break;
        case 6:
            $name = $faker->sauceName();
            break;
    }
    return [
        'name' => $name,
        'emergency_quantity' => rand(15,30),
    ];
});
