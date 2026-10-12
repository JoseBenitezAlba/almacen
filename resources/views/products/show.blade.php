@extends('layouts.app')

@section('title', $product->name)

@section('content')
    <div class="page-head">
        <div>
            <h1>{{ $product->name }}</h1>
            <p>Código {{ $product->code }}</p>
        </div>

        <div class="actions">
            @if (auth()->user()->isAdmin())
                <a class="btn" href="{{ route('movements.create', $product) }}">Registrar movimiento</a>
                <a class="btn btn-ghost" href="{{ route('products.edit', $product) }}">Editar</a>
            @endif
            <a class="btn btn-ghost" href="{{ route('products.index') }}">Volver</a>
        </div>
    </div>

    <div class="card">
        <dl class="detail-list">
            <div>
                <dt>Stock actual</dt>
                <dd>@include('partials.stock-badge', ['product' => $product])</dd>
            </div>
            <div>
                <dt>Stock mínimo</dt>
                <dd>{{ $product->min_stock }} {{ $product->unit }}</dd>
            </div>
            <div>
                <dt>Precio de venta</dt>
                <dd>{{ $product->price_formatted }}</dd>
            </div>
            <div>
                <dt>Categoría</dt>
                <dd>{{ $product->category?->name ?? '—' }}</dd>
            </div>
            <div>
                <dt>Ubicación</dt>
                <dd>{{ $product->location ?? '—' }}</dd>
            </div>
        </dl>

        @if ($product->description)
            <div class="card-body" style="border-top: 1px solid var(--border);">
                <p>{{ $product->description }}</p>
            </div>
        @endif
    </div>

    <div class="card mt">
        <div class="card-head">
            <h2>Últimos movimientos</h2>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Fecha</th>
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
                        <tr><td colspan="5" class="empty">Sin movimientos todavía.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection