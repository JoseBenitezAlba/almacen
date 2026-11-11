<?php

namespace App\Models;

use App\Support\Money;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleItem extends Model
{
    protected $fillable = [
        'sale_id',
        'product_id',
        'quantity',
        'unit_price_cents',
    ];

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getLineTotalCentsAttribute(): int
    {
        return $this->quantity * $this->unit_price_cents;
    }

    public function getUnitPriceFormattedAttribute(): string
    {
        return Money::format($this->unit_price_cents);
    }

    public function getLineTotalFormattedAttribute(): string
    {
        return Money::format($this->line_total_cents);
    }
}
