<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('q');

        $products = Product::query()
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%")
                      ->orWhere('category', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('products.index', compact('products', 'search'));
    }

    public function create(): View
    {
        return view('products.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'code'         => ['required', 'string', 'max:50', 'unique:products,code'],
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category'    => ['nullable', 'string', 'max:100'],
            'location'    => ['nullable', 'string', 'max:100'],
            'unit'        => ['required', 'string', 'max:20'],
            'min_stock'   => ['required', 'integer', 'min:0'],
        ]);

        Product::create($data);

        return redirect()
            ->route('products.index')
            ->with('success', 'Producto creado correctamente.');
    }

    public function show(Product $product): View
    {
        $movements = $product->movements()
            ->with('user')
            ->latest()
            ->limit(20)
            ->get();

        return view('products.show', compact('product', 'movements'));
    }

    public function edit(Product $product): View
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate([
            'code'         => ['required', 'string', 'max:50', Rule::unique('products', 'code')->ignore($product->id)],
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category'    => ['nullable', 'string', 'max:100'],
            'location'    => ['nullable', 'string', 'max:100'],
            'unit'        => ['required', 'string', 'max:20'],
            'min_stock'   => ['required', 'integer', 'min:0'],
        ]);

        $product->update($data);

        return redirect()
            ->route('products.index')
            ->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        // Borrar el producto borraría en cascada todo su historial de movimientos
        if ($product->movements()->exists()) {
            return redirect()
                ->route('products.show', $product)
                ->with('error', 'No se puede eliminar: el producto tiene movimientos registrados.');
        }

        $product->delete();

        return redirect()
            ->route('products.index')
            ->with('success', 'Producto eliminado.');
    }
}
