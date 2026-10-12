<?php

namespace App\Http\Controllers;

use App\Models\Category;
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
            ->with('category')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%")
                      ->orWhereHas('category', fn ($c) => $c->where('name', 'like', "%{$search}%"));
                });
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('products.index', compact('products', 'search'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get();

        return view('products.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->payload($request->validate($this->rules(), $this->messages()));

        Product::create($data);

        return redirect()
            ->route('products.index')
            ->with('success', 'Producto creado correctamente.');
    }

    public function show(Product $product): View
    {
        $product->load('category');

        $movements = $product->movements()
            ->with('user')
            ->latest()
            ->limit(20)
            ->get();

        return view('products.show', compact('product', 'movements'));
    }

    public function edit(Product $product): View
    {
        $categories = Category::orderBy('name')->get();

        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $this->payload($request->validate($this->rules($product), $this->messages()));

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

    private function rules(?Product $product = null): array
    {
        return [
            'code'        => ['required', 'string', 'max:50', Rule::unique('products', 'code')->ignore($product?->id)],
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'location'    => ['nullable', 'string', 'max:100'],
            'unit'        => ['required', 'string', 'max:20'],
            'price'       => ['required', 'regex:/^\d{1,7}([.,]\d{1,2})?$/'],
            'min_stock'   => ['required', 'integer', 'min:0'],
        ];
    }

    private function messages(): array
    {
        return [
            'price.regex' => 'El precio debe ser un número con hasta 2 decimales, por ejemplo 12,50.',
        ];
    }

    // Convierte el precio en euros del formulario a céntimos
    private function payload(array $data): array
    {
        $data['price_cents'] = Product::eurosToCents($data['price']);
        unset($data['price']);

        return $data;
    }
}