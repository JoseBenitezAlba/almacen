<?php

namespace App\Models;

use App\Support\Money;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    protected $fillable = [
        'user_id',
        'customer_name',
        'total_cents',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    // $sale->number -> "#0041"
    public function getNumberAttribute(): string
    {
        return '#' . str_pad((string) $this->id, 4, '0', STR_PAD_LEFT);
    }

    public function getTotalFormattedAttribute(): string
    {
        return Money::format($this->total_cents);
    }
}
