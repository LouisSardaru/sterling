<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Model;
use Spatie\Activitylog\ActivitylogServiceProvider;
use Carbon\Carbon;

class Order extends Model
{
    use SoftDeletes;

    const STATUS_UNASSIGNED = 1;
    const STATUS_PREPARING = 2;
    const STATUS_READY_FOR_PICKUP = 3;
    const STATUS_DELIVERED = 4;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    protected $fillable = [
        'status', 
        'restaurant_table_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
      'created_at' => 'datetime:d-m-Y H:00'
  ];
  
    protected $appends = [
      'total',
      'total_unformatted',
    ];

    public function recipes() {
        return $this->hasManyThrough(Recipe::class, OrdersRecipe::class);
    }

    public function order_recipes() {
        return $this->hasMany(OrdersRecipe::class);
    }

    public function restaurantTable() {
        return $this->belongsTo(RestaurantTable::class);
    }

    public function getTotalUnformattedAttribute() {
        $orderRecipes = OrdersRecipe::where('order_id', $this->id)->get();

        $total = 0;
        foreach ($orderRecipes as $orderRecipe) {
            $total += $orderRecipe->price * $orderRecipe->quantity;
        }

        return $total;
    }

    public function getTotalAttribute() {
        $orderRecipes = OrdersRecipe::where('order_id', $this->id)->get();

        $total = 0;
        foreach ($orderRecipes as $orderRecipe) {
            $total += $orderRecipe->price * $orderRecipe->quantity;
        }

        return number_format($total, 2);
    }

    public function scopeOrderDescByDate($query) {
        $query->orderByDesc('created_at');
    }

    public function scopeFilter($query, array $filters) {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            return $query->whereHas('restaurantTable', function ($q) use ($search){
                return $q->whereHas('user', function ($q) use ($search) {
                  return $q->where('first_name', 'like', '%'.$search.'%')
                      ->orWhere('last_name', 'like', '%'.$search.'%');
                });
            });
        })->when($filters['trashed'] ?? null, function ($query, $trashed) {
            if ($trashed === 'with') {
                $query->withTrashed();
            } elseif ($trashed === 'only') {
                $query->onlyTrashed();
            }
        })->when($filters['type'] ?? null, function ($query, $type) {
            if ($type === 'dinein') {
                $query->whereDineIn();
            } elseif ($type === 'takeout') {
                $query->whereTakeout();
            }
        })->when($filters['status'] ?? null, function ($query, $status) {
            if ($status === 'unassigned') {
                $query->whereUnassigned();
            } elseif ($status === 'preparing') {
                $query->wherePreparing();
            } elseif ($status === 'ready') {
                $query->whereReadyForPickup();
            } elseif ($status === 'delivered') {
                $query->whereDelivered();
            }
        })->when($filters['user'] ?? null, function ($query, $user) {
            if ($user !== 'all') {
              // user scope
            }
        })->when($filters['restaurant_table'] ?? null, function ($query, $table) {
            if ($table !== 'all') {
                $query->where('restaurant_table_id', $table);
            }
        });
    }

    public function activities() {
        return $this->morphMany(ActivitylogServiceProvider::determineActivityModel(), 'subject');
    }

    public function scopeWherePlacedToday($query) {
        return $query->whereDate('created_at', Carbon::today());
    }

    public function scopeWhereTakeoutToday($query) {
        return $query->whereDate('created_at', Carbon::today())->whereNull('restaurant_table_id');
    }

    public function scopeWhereDeliveredToday($query) {
        return $query->whereDate('created_at', Carbon::today())->where('status', self::STATUS_DELIVERED);
    }

    public function scopeWhereUnassigned($query) {
        return $query->where('status', self::STATUS_UNASSIGNED);
    }

    public function scopeWherePreparing($query) {
        return $query->where('status', self::STATUS_PREPARING);
    }

    public function scopeWhereReadyForPickup($query) {
        return $query->where('status', self::STATUS_READY_FOR_PICKUP);
    }

    public function scopeWhereDelivered($query) {
        return $query->where('status', self::STATUS_DELIVERED);
    }

    public function scopeWhereDineIn($query) {
        return $query->whereNotNull('restaurant_table_id');
    }

    public function scopeWhereTakeout($query) {
      return $query->whereNull('restaurant_table_id');
  }
}
