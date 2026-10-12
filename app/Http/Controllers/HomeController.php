<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $totalProducts = Product::count();
        $movementsToday = StockMovement::whereDate('created_at', today())->count();

        $lowStockProducts = Product::whereColumn('stock', '<=', 'min_stock')
            ->orderBy('name')
            ->get();

        $latestMovements = StockMovement::with(['product', 'user'])
            ->latest()
            ->limit(8)
            ->get();

        return view('home', compact(
            'totalProducts',
            'movementsToday',
            'lowStockProducts',
            'latestMovements'
        ));
    }
}