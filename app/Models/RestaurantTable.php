<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Model;
use App\User;
use Spatie\Activitylog\ActivitylogServiceProvider;

class RestaurantTable extends Model
{
    use SoftDeletes;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    protected $fillable = [
        'table_number',
        'user_id'
    ];

    public function user() {
      return $this->belongsTo(User::class);
    }

    public function scopeOrderByTableNumber($query) {
        $query->orderBy('table_number');
    }

    public function scopeWhereUnassigned($query) {
        return $query->whereNull('user_id');
    }

    public function scopeWhereAssigned($query) {
        return $query->whereNotNull('user_id');
    }

    public function scopeFilter($query, array $filters) {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            return $query->whereHas('user', function ($q) use ($search) {
              return $q->where('first_name', 'like', '%'.$search.'%')
                  ->orWhere('last_name', 'like', '%'.$search.'%');
            })->orWhere('table_number', $search);
        })->when($filters['trashed'] ?? null, function ($query, $trashed) {
            if ($trashed === 'with') {
                $query->withTrashed();
            } elseif ($trashed === 'only') {
                $query->onlyTrashed();
            }
        })->when($filters['status'] ?? null, function ($query, $status) {
            if ($status === 'assigned') {
                $query->whereAssigned();
            } else if ($status === 'unassigned') {
                $query->whereUnassigned();
            }
        });
    }

    public function activities()
    {
        return $this->morphMany(ActivitylogServiceProvider::determineActivityModel(), 'subject');
    }
}
