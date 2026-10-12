<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'category_id',
        'location',
        'unit',
        'price_cents',
        'stock',
        'min_stock',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function isLowStock(): bool
    {
        return $this->stock <= $this->min_stock;
    }

    // $product->price_formatted → "12,50 €"
    public function getPriceFormattedAttribute(): string
    {
        return number_format($this->price_cents / 100, 2, ',', '.') . ' €';
    }

    // "12,50" o "12.5" → 1250
    public static function eurosToCents(string $value): int
    {
        return (int) round(((float) str_replace(',', '.', trim($value))) * 100);
    }
}