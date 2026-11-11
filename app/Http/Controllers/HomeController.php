<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Models\StockMovement;
use App\Support\Money;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $monthStart = now()->startOfMonth();

        $salesMonthTotal = Money::format((int) Sale::where('created_at', '>=', $monthStart)->sum('total_cents'));
        $salesMonthCount = Sale::where('created_at', '>=', $monthStart)->count();

        $totalProducts = Product::count();
        $inventoryValue = Money::format((int) Product::sum(DB::raw('stock * price_cents')));
        $movementsToday = StockMovement::whereDate('created_at', today())->count();

        $lowStockProducts = Product::whereColumn('stock', '<=', 'min_stock')
            ->orderBy('name')
            ->get();

        $latestSales = Sale::with('user')
            ->withCount('items')
            ->latest()
            ->limit(5)
            ->get();

        $latestMovements = StockMovement::with(['product', 'user'])
            ->latest()
            ->limit(6)
            ->get();

        return view('home', [
            'salesMonthTotal'  => $salesMonthTotal,
            'salesMonthCount'  => $salesMonthCount,
            'totalProducts'    => $totalProducts,
            'inventoryValue'   => $inventoryValue,
            'movementsToday'   => $movementsToday,
            'lowStockProducts' => $lowStockProducts,
            'latestSales'      => $latestSales,
            'latestMovements'  => $latestMovements,
            'salesWeek'        => $this->salesChart(),
            'week'             => $this->movementsChart(),
            'categoriesChart'  => $this->categoriesChart(),
        ]);
    }

    // Ingresos (en euros) de cada uno de los últimos 7 días
    private function salesChart(): array
    {
        $start = today()->subDays(6);

        $sales = Sale::where('created_at', '>=', $start)
            ->get(['total_cents', 'created_at'])
            ->groupBy(fn (Sale $s) => $s->created_at->toDateString());

        $labels = [];
        $values = [];

        for ($i = 0; $i < 7; $i++) {
            $day = $start->copy()->addDays($i);
            $items = $sales->get($day->toDateString(), collect());

            $labels[] = $day->format('d/m');
            $values[] = round($items->sum('total_cents') / 100, 2);
        }

        return compact('labels', 'values');
    }

    // Unidades que entran y salen en cada uno de los últimos 7 días
    private function movementsChart(): array
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
}
