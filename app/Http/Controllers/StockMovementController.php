<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class StockMovementController extends Controller
{
    public function index(): View
    {
        $movements = StockMovement::with(['product', 'user'])
            ->latest()
            ->paginate(20);

        return view('movements.index', compact('movements'));
    }

    public function create(Product $product): View
    {
        return view('movements.create', compact('product'));
    }

    public function store(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'type'     => ['required', Rule::in([StockMovement::TYPE_IN, StockMovement::TYPE_OUT])],
            'quantity' => ['required', 'integer', 'min:1'],
            'reason'   => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($product, $data) {
            // Se vuelve a leer el producto dentro de la transacción para trabajar con el stock real
            $current = Product::whereKey($product->id)->lockForUpdate()->firstOrFail();

            if ($data['type'] === StockMovement::TYPE_OUT && $data['quantity'] > $current->stock) {
                throw ValidationException::withMessages([
                    'quantity' => "No hay stock suficiente. Stock actual: {$current->stock} {$current->unit}.",
                ]);
            }

            $current->stock += $data['type'] === StockMovement::TYPE_IN
                ? $data['quantity']
                : -$data['quantity'];
            $current->save();

            $current->movements()->create([
                'user_id'  => auth()->id(),
                'type'     => $data['type'],
                'quantity' => $data['quantity'],
                'reason'   => $data['reason'] ?? null,
            ]);
        });

        return redirect()
            ->route('products.show', $product)
            ->with('success', 'Movimiento registrado correctamente.');
    }
}