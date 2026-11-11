<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\StockMovement;
use App\Services\StockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SaleController extends Controller
{
    public function index(): View
    {
        $sales = Sale::with('user')
            ->withCount('items')
            ->latest()
            ->paginate(15);

        return view('sales.index', compact('sales'));
    }

    public function create(): View
    {
        $products = Product::where('stock', '>', 0)
            ->orderBy('name')
            ->get();

        return view('sales.create', compact('products'));
    }

    public function store(Request $request, StockService $stock): RedirectResponse
    {
        $data = $request->validate([
            'customer_name'      => ['nullable', 'string', 'max:100'],
            'items'              => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity'   => ['required', 'integer', 'min:1'],
        ], [
            'items.required'              => 'Añade al menos un producto a la venta.',
            'items.min'                   => 'Añade al menos un producto a la venta.',
            'items.*.product_id.required' => 'Elige un producto en cada línea.',
            'items.*.product_id.exists'   => 'El producto elegido no existe.',
            'items.*.quantity.required'   => 'Indica la cantidad de cada línea.',
            'items.*.quantity.integer'    => 'La cantidad debe ser un número entero.',
            'items.*.quantity.min'        => 'La cantidad debe ser al menos 1.',
        ]);

        $sale = DB::transaction(function () use ($data, $request, $stock) {
            $sale = Sale::create([
                'user_id'       => $request->user()->id,
                'customer_name' => $data['customer_name'] ?? null,
                'total_cents'   => 0,
            ]);

            $total = 0;

            foreach ($data['items'] as $line) {
                $quantity = (int) $line['quantity'];

                // Descuenta el stock y devuelve el producto con su precio ACTUAL de la base de datos
                $product = $stock->move(
                    (int) $line['product_id'],
                    StockMovement::TYPE_OUT,
                    $quantity,
                    "Venta {$sale->number}",
                    $request->user()->id,
                    'items'
                );

                $sale->items()->create([
                    'product_id'       => $product->id,
                    'quantity'         => $quantity,
                    'unit_price_cents' => $product->price_cents,
                ]);

                $total += $product->price_cents * $quantity;
            }

            $sale->update(['total_cents' => $total]);

            return $sale;
        });

        return redirect()
            ->route('sales.show', $sale)
            ->with('success', "Venta {$sale->number} registrada.");
    }

    public function show(Sale $sale): View
    {
        $sale->load(['user', 'items.product']);

        return view('sales.show', compact('sale'));
    }
}
