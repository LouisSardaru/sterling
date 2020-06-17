<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\IngredientsRecipe;
use App\Model;
use Spatie\Activitylog\ActivitylogServiceProvider;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Recipe extends Model implements HasMedia
{

    use SoftDeletes, InteractsWithMedia;
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    protected $fillable = [
        'name',
        'price',
        'description',
        'calories',
        'dietary_restrictions'
    ];

    protected $appends = [
      'short_description',
      'formatted_price',
      'image',
      'thumbnail',
    ];

     /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'name' => 'string',
        'price' => 'double'
    ];

    public function ingredients() {
        return $this->hasManyThrough(Ingredient::class, IngredientsRecipe::class);
    }

    public function ingredients_recipe() {
        return $this->hasMany(IngredientsRecipe::class);
    }

    public function order_recipes() {
      return $this->hasMany(OrdersRecipe::class);
  }

    public function getShortDescriptionAttribute() {
      return \Str::limit($this->description, 50);
    }

    public function getFormattedPriceAttribute() {
      return number_format($this->price, 2);
    }

    public function registerMediaConversions(Media $media = null): void {
        $this->addMediaConversion('thumb')
            ->width(200)
            ->height(200);
    }

    public function registerMediaCollections(): void {
        $this->addMediaCollection('product')->singleFile();
    }

    public function getImageAttribute() {
        $mainImage = $this->getMedia('product');

        if ($mainImage->isNotEmpty()) {
            $mainImageUrl = $mainImage[0]->getUrl();
            return $mainImageUrl;
        }

        return 'https://placehold.it/1000x1000?text=Recipe';
    }

    public function getThumbnailAttribute() {
        $thumbnail = $this->getMedia('product');

        if($thumbnail->isNotEmpty()) {
            $thumbnailUrl = $thumbnail[0]->getUrl('thumb');
            return $thumbnailUrl;
        }

        return 'https://placehold.it/200x200?text=' . strtoupper(substr($this->name, 0, 1));
    }

    public function scopeOrderByName($query) {
        $query->orderBy('name')->orderBy('name');
    }

    public function scopeWhereAddedToOrder($query, $id) {
        return $query->where(function ($query) use ($id) {
            return $query->whereHas('order_recipes', function ($query) use ($id) {
                return $query->where('order_id', $id);
            });
        });
    }

    public function scopeFilter($query, array $filters) {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%'.$search.'%')
                    ->orWhere('dietary_restrictions', 'like', '%'.$search.'%');
            });
        })->when($filters['trashed'] ?? null, function ($query, $trashed) {
            if ($trashed === 'with') {
                $query->withTrashed();
            } elseif ($trashed === 'only') {
                $query->onlyTrashed();
            }
        });
    }

    public function activities()
    {
        return $this->morphMany(ActivitylogServiceProvider::determineActivityModel(), 'subject');
    }
}
