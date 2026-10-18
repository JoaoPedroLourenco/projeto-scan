<?php

namespace App\Models;

use App\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'barcode',
        'description',
        'price',
        'min_stock',
        'is_active',
    ];

    public function scopeFilter(Builder $query, QueryFilter $filters): Builder {
        return $filters->apply($query);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function allergens(): BelongsToMany
    {
        return $this->belongsToMany(Allergen::class);
    }
}
