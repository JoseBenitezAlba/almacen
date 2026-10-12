<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $totalProducts = Product::count();
        $inventoryValue = $this->money((int) Product::sum(DB::raw('stock * price_cents')));
        $movementsToday = StockMovement::whereDate('created_at', today())->count();

        $lowStockProducts = Product::whereColumn('stock', '<=', 'min_stock')
            ->orderBy('name')
            ->get();

        $latestMovements = StockMovement::with(['product', 'user'])
            ->latest()
            ->limit(8)
            ->get();

        return view('home', [
            'totalProducts'    => $totalProducts,
            'inventoryValue'   => $inventoryValue,
            'movementsToday'   => $movementsToday,
            'lowStockProducts' => $lowStockProducts,
            'latestMovements'  => $latestMovements,
            'week'             => $this->weekChart(),
            'categoriesChart'  => $this->categoriesChart(),
        ]);
    }

    // Unidades que entran y salen en cada uno de los últimos 7 días
    private function weekChart(): array
    {
        $start = today()->subDays(6);

        $movements = StockMovement::where('created_at', '>=', $start)
            ->get(['type', 'quantity', 'created_at'])
            ->groupBy(fn (StockMovement $m) => $m->created_at->toDateString());

        $labels = [];
        $in = [];
        $out = [];

        for ($i = 0; $i < 7; $i++) {
            $day = $start->copy()->addDays($i);
            $items = $movements->get($day->toDateString(), collect());

            $labels[] = $day->format('d/m');
            $in[] = (int) $items->where('type', StockMovement::TYPE_IN)->sum('quantity');
            $out[] = (int) $items->where('type', StockMovement::TYPE_OUT)->sum('quantity');
        }

        return compact('labels', 'in', 'out');
    }

    // Unidades en stock por categoría
    private function categoriesChart(): array
    {
        $rows = Category::withSum('products', 'stock')
            ->orderBy('name')
            ->get()
            ->filter(fn (Category $c) => (int) $c->products_sum_stock > 0)
            ->values();

        $labels = $rows->pluck('name')->all();
        $values = $rows->map(fn (Category $c) => (int) $c->products_sum_stock)->all();

        $withoutCategory = (int) Product::whereNull('category_id')->sum('stock');

        if ($withoutCategory > 0) {
            $labels[] = 'Sin categoría';
            $values[] = $withoutCategory;
        }

        return compact('labels', 'values');
    }

    private function money(int $cents): string
    {
        return number_format($cents / 100, 2, ',', '.') . ' €';
    }
}