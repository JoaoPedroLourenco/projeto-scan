<?php

namespace App\Models;

use App\Enums\StockMovementType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StockMovement extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'stock_batch_id',
        'user_id',
        'type',
        'quantity',
        'unit_price',
        'reason',
        'notes',
    ];

    protected $casts = [
        'type' => StockMovementType::class,
    ];

    public function stockBatch(): BelongsTo
    {
        return $this->belongsTo(StockBatch::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
