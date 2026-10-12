@extends('layouts.app')

@section('title', 'Movimientos')

@section('content')
    <div class="page-head">
        <div>
            <h1>Movimientos</h1>
            <p>Historial de entradas y salidas de todo el almacén</p>
        </div>
    </div>

    <div class="card">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Producto</th>
                        <th>Tipo</th>
                        <th>Cantidad</th>
                        <th>Motivo</th>
                        <th>Usuario</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($movements as $movement)
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
                            <td>{{ $movement->reason ?? '—' }}</td>
                            <td>{{ $movement->user->name ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="empty">Todavía no hay movimientos.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @include('partials.pager', ['paginator' => $movements])
@endsection