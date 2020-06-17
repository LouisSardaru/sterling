<?php

use App\User;
use App\Models\Ingredient;
use App\Models\IngredientsRecipe;
use App\Models\Recipe;
use App\Models\RestaurantTable;
use App\Models\Order;
use App\Models\OrdersRecipe;
use App\Organization;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            ACLSeeder::class
        ]);

        $ingredients = factory(Ingredient::class, 50)->create();
        $ingredients->each(function ($ingredient) {
            $ingredient->clearStock(rand(0,30));
        });

        $recipes = factory(Recipe::class,50)->create();

        $recipes->each(function (Recipe $recipe) use ($ingredients) {
            for ($i = 0; $i<rand(1,4); $i++) {
                $ingredients_recipe = new IngredientsRecipe();
                $ingredients_recipe->quantity = rand(1,5);
                $ingredients_recipe->ingredient_id = $ingredients->shuffle()->first()->id;
                $ingredients_recipe->recipe_id = $recipe->id;
                $ingredients_recipe->save();
            }

        });

        $restaurant_tables = factory(RestaurantTable::class, 20)->create();
        foreach($restaurant_tables as $index => $restaurant_table) {
          $restaurant_table->table_number = $index + 1;
          $restaurant_table->save();
        }

        factory(Order::class, 50)->create();

        factory(OrdersRecipe::class, 50)->create();
    }
}
