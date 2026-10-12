<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::withCount('products')
            ->orderBy('name')
            ->get();

        return view('categories.index', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:categories,name'],
        ]);

        Category::create($data);

        return redirect()
            ->route('categories.index')
            ->with('success', 'Categoría creada.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->products()->exists()) {
            return redirect()
                ->route('categories.index')
                ->with('error', 'No se puede eliminar: tiene productos asignados.');
        }

        $category->delete();

        return redirect()
            ->route('categories.index')
            ->with('success', 'Categoría eliminada.');
    }
}