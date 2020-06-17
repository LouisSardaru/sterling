<?php

namespace App\Models;

use App\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Appstract\Stock\HasStock;
use Appstract\Stock\StockMutation;
use Spatie\Activitylog\ActivitylogServiceProvider;


class Ingredient extends Model
{
    use SoftDeletes, HasStock;
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    protected $appends = ['quantity'];

    protected $fillable = [
        'name',
        'emergency_quantity'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'name' => 'string',
        'emergency_quantity' => 'integer'
    ];

    public function recipes() {
        return $this->hasManyThrough(Recipe::class, IngredientsRecipe::class);
    }

    public function ingredients_recipe() {
        return $this->hasMany(IngredientsRecipe::class);
    }

    public function getQuantityAttribute(){
        return StockMutation::where('stockable_type', Ingredient::class)->where('stockable_id', $this->id)->sum('amount');
    }

    public function scopeWhereInStock($query)
    {
        return $query->where(function ($query) {
            return $query->whereHas('stockMutations', function ($query) {
                return $query->select('stockable_id')
                    ->groupBy('stockable_id')
                    ->havingRaw('SUM(amount) > ingredients.emergency_quantity');
            });
        });
    }

    public function scopeWhereOnEmergencyStock($query) {
        return $query->where(function ($query) {
            return $query->whereHas('stockMutations', function ($query) {
                return $query->select('stockable_id')
                    ->groupBy('stockable_id')
                    ->havingRaw('ingredients.emergency_quantity > SUM(amount) and SUM(amount) > 0');
            });
        });
    }

    public function scopeWhereAddedToRecipe($query, $id) {
      return $query->where(function ($query) use ($id) {
          return $query->whereHas('ingredients_recipe', function ($query) use ($id) {
              return $query->where('recipe_id', $id);
          });
      });
    }

    public function scopeOrderByName($query) {
        $query->orderBy('name');
    }

    public function scopeFilter($query, array $filters) {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%'.$search.'%');
            });
        })->when($filters['trashed'] ?? null, function ($query, $trashed) {
            if ($trashed === 'with') {
                $query->withTrashed();
            } elseif ($trashed === 'only') {
                $query->onlyTrashed();
            }
        })->when($filters['stock'] ?? null, function ($query, $stock) {
            if ($stock === 'in') {
                $query->whereInStock();
            } elseif ($stock === 'emergency') {
                $query->whereOnEmergencyStock();
            } elseif ($stock === 'out') {
                $query->whereOutOfStock();
            }
        });
    }

    public function activities()
    {
        return $this->morphMany(ActivitylogServiceProvider::determineActivityModel(), 'subject');
    }
}
