@extends('layouts.app')

@section('title', 'Inicio')

@section('content')
    <div class="page-head">
        <div>
            <h1>Resumen</h1>
            <p>Ventas, existencias y lo que necesita tu atención</p>
        </div>

        <div class="actions">
            <a class="btn" href="{{ route('sales.create') }}">+ Registrar venta</a>
            @if (auth()->user()->isAdmin())
                <a class="btn btn-ghost" href="{{ route('products.create') }}">Nuevo producto</a>
            @endif
        </div>
    </div>

    <div class="stats">
        <div class="card stat-card">
            <span class="stat-label">Ventas del mes</span>
            <span class="stat-value">{{ $salesMonthTotal }}</span>
            <span class="stat-sub">{{ $salesMonthCount }} {{ $salesMonthCount === 1 ? 'venta' : 'ventas' }}</span>
        </div>
        <div class="card stat-card">
            <span class="stat-label">Productos</span>
            <span class="stat-value">{{ $totalProducts }}</span>
            <span class="stat-sub">referencias en catálogo</span>
        </div>
        <div class="card stat-card">
            <span class="stat-label">Valor del inventario</span>
            <span class="stat-value">{{ $inventoryValue }}</span>
            <span class="stat-sub">a precio de venta</span>
        </div>
        <div class="card stat-card {{ $lowStockProducts->count() > 0 ? 'is-warn' : '' }}">
            <span class="stat-label">Stock bajo</span>
            <span class="stat-value">{{ $lowStockProducts->count() }}</span>
            <span class="stat-sub">productos por reponer</span>
        </div>
        <div class="card stat-card">
            <span class="stat-label">Movimientos hoy</span>
            <span class="stat-value">{{ $movementsToday }}</span>
            <span class="stat-sub">entradas y salidas</span>
        </div>
    </div>

    <div class="grid-2">
        <div class="card">
            <div class="card-head">
                <h2>Evolución de ventas</h2>
                <p>Ingresos diarios de los últimos 7 días</p>
            </div>
            <div class="chart-box"><canvas id="salesChart"></canvas></div>
        </div>

        <div class="card">
            <div class="card-head">
                <h2>Stock por categoría</h2>
                <p>Unidades disponibles</p>
            </div>
            <div class="chart-box"><canvas id="categoryChart"></canvas></div>
        </div>
    </div>

    <div class="grid-2">
        <div class="card">
            <div class="card-head">
                <h2>Últimas ventas</h2>
                <p>La actividad más reciente de la tienda</p>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Venta</th>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($latestSales as $sale)
                            <tr>
                                <td><a href="{{ route('sales.show', $sale) }}"><strong>{{ $sale->number }}</strong></a></td>
                                <td>{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                                <td>{{ $sale->customer_name ?? 'Venta en tienda' }}</td>
                                <td><strong>{{ $sale->total_formatted }}</strong></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="empty">Todavía no hay ventas.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-body" style="padding-top: 12px; border-top: 1px solid var(--border);">
                <a class="btn btn-ghost btn-sm" href="{{ route('sales.index') }}">Ver todas →</a>
            </div>
        </div>

        <div class="card">
            <div class="card-head">
                <h2>Necesitan reposición</h2>
                <p>Por debajo del stock mínimo</p>
            </div>
            <ul class="list">
                @forelse ($lowStockProducts->take(6) as $product)
                    <li>
                        <div>
                            <a href="{{ route('products.show', $product) }}"><strong>{{ $product->name }}</strong></a>
                            <div class="muted" style="font-size: .8rem;">{{ $product->code }} · mínimo {{ $product->min_stock }}</div>
                        </div>
                        @include('partials.stock-badge', ['product' => $product])
                    </li>
                @empty
                    <li class="muted">Todo en orden: nada por debajo del mínimo.</li>
                @endforelse
            </ul>
        </div>
    </div>

    <div class="grid-2">
        <div class="card">
            <div class="card-head">
                <h2>Últimos movimientos</h2>
                <p>Entradas y salidas de stock</p>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Producto</th>
                            <th>Tipo</th>
                            <th>Cantidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($latestMovements as $movement)
                            <tr>
                                <td>{{ $movement->created_at->format('d/m/Y H:i') }}</td>
                                <td><a href="{{ route('products.show', $movement->product) }}">{{ $movement->product->name }}</a></td>
                                <td>
                                    <span class="badge {{ $movement->type === 'in' ? 'badge-ok' : 'badge-danger' }}">
                                        {{ $movement->type === 'in' ? 'Entrada' : 'Salida' }}
                                    </span>
                                </td>
                                <td class="{{ $movement->type === 'in' ? 'qty-in' : 'qty-out' }}">
                                    {{ $movement->type === 'in' ? '+' : '−' }}{{ $movement->quantity }}
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="empty">Todavía no hay movimientos.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-head">
                <h2>Movimientos de la semana</h2>
                <p>Unidades que entran y salen</p>
            </div>
            <div class="chart-box"><canvas id="movementsChart"></canvas></div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <script>
        const salesWeek = @json($salesWeek);
        const week = @json($week);
        const categories = @json($categoriesChart);

        function euros(value) {
            return value.toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' €';
        }

        new Chart(document.getElementById('salesChart'), {
            type: 'line',
            data: {
                labels: salesWeek.labels,
                datasets: [{
                    label: 'Ventas',
                    data: salesWeek.values,
                    borderColor: '#0d9488',
                    backgroundColor: 'rgba(13, 148, 136, 0.12)',
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#0d9488',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, ticks: { callback: function (v) { return v + ' €'; } } },
                },
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: function (ctx) { return euros(ctx.parsed.y); } } },
                },
            },
        });

        new Chart(document.getElementById('movementsChart'), {
            type: 'bar',
            data: {
                labels: week.labels,
                datasets: [
                    { label: 'Entradas', data: week.in, backgroundColor: '#16a34a', borderRadius: 4 },
                    { label: 'Salidas', data: week.out, backgroundColor: '#dc2626', borderRadius: 4 },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
                plugins: { legend: { position: 'bottom' } },
            },
        });

        const categoryBox = document.getElementById('categoryChart');

        if (categories.values.length) {
            new Chart(categoryBox, {
                type: 'doughnut',
                data: {
                    labels: categories.labels,
                    datasets: [{
                        data: categories.values,
                        backgroundColor: ['#0d9488', '#6366f1', '#f59e0b', '#ef4444', '#0ea5e9', '#84cc16', '#a855f7', '#64748b'],
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' } },
                },
            });
        } else {
            categoryBox.parentElement.innerHTML = '<p class="empty">Aún no hay stock que mostrar.</p>';
        }
    </script>
@endpush
