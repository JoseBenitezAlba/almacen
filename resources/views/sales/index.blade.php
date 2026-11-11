@extends('layouts.app')

@section('title', 'Ventas')

@section('content')
    <div class="page-head">
        <div>
            <h1>Ventas</h1>
            <p>Historial de ventas registradas</p>
        </div>

        <div class="actions">
            <a class="btn" href="{{ route('sales.create') }}">+ Registrar venta</a>
        </div>
    </div>

    <div class="card">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Venta</th>
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th>Vendedor</th>
                        <th>Líneas</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sales as $sale)
                        <tr>
                            <td><a href="{{ route('sales.show', $sale) }}"><strong>{{ $sale->number }}</strong></a></td>
                            <td>{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $sale->customer_name ?? 'Venta en tienda' }}</td>
                            <td>{{ $sale->user->name ?? '—' }}</td>
                            <td>{{ $sale->items_count }}</td>
                            <td><strong>{{ $sale->total_formatted }}</strong></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="empty">Todavía no hay ventas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @include('partials.pager', ['paginator' => $sales])
@endsection
