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

$factory->define(App\Models\Recipe::class, function (Faker $faker) {
    $faker->addProvider(new \FakerRestaurant\Provider\en_US\Restaurant($faker));

    $dietary_restrictions = [
      'Dairy free, Organic',
      'Dairy free',
      'Vegan',
      'Vegetarian',
      'Low carb, Vegetarian',
      'Gluten free',
      'Gluten free, Vegan',
      'Sugar free',
      'Contains nuts'
    ];

    return [
        'name' => $faker->foodName(),
        'description' => $faker->sentence(),
        'dietary_restrictions' => $faker->randomElement($dietary_restrictions),
        'calories' => rand(50,2500),
        'price' => rand(15,200),
    ];
});
