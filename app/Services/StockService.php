<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Validation\ValidationException;

class StockService
{
    /**
     * Registra una entrada o salida de stock y devuelve el producto actualizado.
     *
     * IMPORTANTE: hay que llamarlo dentro de un DB::transaction. La transacción la abre
     * quien llama, para poder agrupar varias operaciones (por ejemplo, las líneas de una venta).
     */
    public function move(
        int $productId,
        string $type,
        int $quantity,
        ?string $reason,
        ?int $userId,
        string $errorKey = 'quantity'
    ): Product {
        // Se relee el producto bloqueándolo para trabajar con el stock real
        $product = Product::whereKey($productId)->lockForUpdate()->firstOrFail();

        if ($type === StockMovement::TYPE_OUT && $quantity > $product->stock) {
            throw ValidationException::withMessages([
                $errorKey => "No hay stock suficiente de {$product->name}. Stock actual: {$product->stock} {$product->unit}.",
            ]);
        }

        $product->stock += $type === StockMovement::TYPE_IN ? $quantity : -$quantity;
        $product->save();

        $product->movements()->create([
            'user_id'  => $userId,
            'type'     => $type,
            'quantity' => $quantity,
            'reason'   => $reason,
        ]);

        return $product;
    }
}
