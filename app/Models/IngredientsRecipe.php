<?php

namespace App\Models;

use App\Model;

class IngredientsRecipe extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    protected $fillable = [
        'ingredient_id',
        'recipe_id',
        'quantity'
    ];


    public function ingredient() {
        return $this->belongsTo(Ingredient::class);
    }

    public function recipe() {
        return $this->belongsTo(Recipe::class);
    }
}
