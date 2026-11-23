<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductLookupController extends Controller
{
    public function __invoke(Request $request): View|RedirectResponse
    {
        $product = null;
        $code = '';

        if ($request->has('code')) {
            $data = $request->validate(['code' => ['required', 'string', 'max:50']]);
            $code = $data['code'];
            $product = Product::with('category')->where('code', $code)->first();

            if ($product) {
                return redirect()->route('products.show', $product);
            }
        }

        return view('products.lookup', ['code' => $code, 'notFound' => $request->has('code')]);
    }
}
