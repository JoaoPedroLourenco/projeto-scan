<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'batch_code',
        'quantity',
        'expiration_date',
        'is_active',
    ];

    protected $casts = [
        'expiration_date',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
