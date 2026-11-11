<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use App\Services\StockService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
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

    public function store(Request $request, Product $product, StockService $stock): RedirectResponse
    {
        $data = $request->validate([
            'type'     => ['required', Rule::in([StockMovement::TYPE_IN, StockMovement::TYPE_OUT])],
            'quantity' => ['required', 'integer', 'min:1'],
            'reason'   => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(fn () => $stock->move(
            $product->id,
            $data['type'],
            (int) $data['quantity'],
            $data['reason'] ?? null,
            $request->user()->id
        ));

        return redirect()
            ->route('products.show', $product)
            ->with('success', 'Movimiento registrado correctamente.');
    }
}
