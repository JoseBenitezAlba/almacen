@extends('layouts.app')

@section('title', 'Venta ' . $sale->number)

@section('content')
    <div class="page-head">
        <div>
            <h1>Venta {{ $sale->number }}</h1>
            <p>
                {{ $sale->created_at->format('d/m/Y H:i') }}
                · {{ $sale->customer_name ?? 'Venta en tienda' }}
                · Vendedor: {{ $sale->user->name ?? '—' }}
            </p>
        </div>

        <div class="actions">
            <a class="btn btn-ghost" href="{{ route('sales.index') }}">Volver</a>
        </div>
    </div>

    <div class="card">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio unitario</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sale->items as $item)
                        <tr>
                            <td><a href="{{ route('products.show', $item->product) }}">{{ $item->product->name }}</a></td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ $item->unit_price_formatted }}</td>
                            <td>{{ $item->line_total_formatted }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" style="text-align: right;"><strong>Total</strong></td>
                        <td><strong>{{ $sale->total_formatted }}</strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endsection
