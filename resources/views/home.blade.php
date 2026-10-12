@extends('layouts.app')

@section('title', 'Inicio')

@section('content')
    <hgroup>
        <h1>Resumen del almacén</h1>
        <p>Vista rápida de cómo está todo</p>
    </hgroup>

    <div class="grid">
        <article>
            <p>Productos</p>
            <p class="stat">{{ $totalProducts }}</p>
        </article>
        <article>
            <p>Con stock bajo</p>
            <p class="stat">{{ $lowStockProducts->count() }}</p>
        </article>
        <article>
            <p>Movimientos hoy</p>
            <p class="stat">{{ $movementsToday }}</p>
        </article>
    </div>

    <p>
        @if (auth()->user()->isAdmin())
            <a href="{{ route('products.create') }}" role="button">Nuevo producto</a>
        @endif
        <a href="{{ route('products.index') }}" role="button" class="secondary">Ver productos</a>
    </p>

    <h2>Productos con stock bajo</h2>

    <figure>
        <table>
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Stock</th>
                    <th>Mínimo</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($lowStockProducts as $product)
                    <tr>
                        <td>{{ $product->code }}</td>
                        <td><a href="{{ route('products.show', $product) }}">{{ $product->name }}</a></td>
                        <td>{{ $product->stock }} {{ $product->unit }}</td>
                        <td>{{ $product->min_stock }}</td>
                        <td>
                            @if (auth()->user()->isAdmin())
                                <a href="{{ route('movements.create', $product) }}">Registrar entrada</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">Todo en orden: ningún producto por debajo del mínimo.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </figure>

    <h2>Últimos movimientos</h2>

    <figure>
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Producto</th>
                    <th>Tipo</th>
                    <th>Cantidad</th>
                    <th>Motivo</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($latestMovements as $movement)
                    <tr>
                        <td>{{ $movement->created_at->format('d/m/Y H:i') }}</td>
                        <td><a href="{{ route('products.show', $movement->product) }}">{{ $movement->product->name }}</a></td>
                        <td class="{{ $movement->type === 'in' ? 'mov-in' : 'mov-out' }}">
                            {{ $movement->type === 'in' ? 'Entrada' : 'Salida' }}
                        </td>
                        <td>{{ $movement->quantity }}</td>
                        <td>{{ $movement->reason }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">Todavía no hay movimientos.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </figure>
@endsection
