@extends('layouts.app')

@section('title', $product->name)

@section('content')
    <hgroup>
        <h1>{{ $product->name }}</h1>
        <p>Código: {{ $product->code }}</p>
    </hgroup>

    <article>
        <p>
            <strong>Stock actual:</strong> {{ $product->stock }} {{ $product->unit }}
            @if ($product->isLowStock())
                <mark>Stock bajo</mark>
            @endif
        </p>
        <p><strong>Stock mínimo:</strong> {{ $product->min_stock }}</p>
        <p><strong>Categoría:</strong> {{ $product->category ?? '—' }}</p>
        <p><strong>Ubicación:</strong> {{ $product->location ?? '—' }}</p>

        @if ($product->description)
            <p>{{ $product->description }}</p>
        @endif
    </article>

    <p>
        @if (auth()->user()->isAdmin())
            <a href="{{ route('movements.create', $product) }}" role="button">Registrar movimiento</a>
            <a href="{{ route('products.edit', $product) }}" role="button" class="secondary">Editar</a>
        @endif
        <a href="{{ route('products.index') }}" role="button" class="secondary">Volver</a>
    </p>

    <h2>Últimos movimientos</h2>

    <figure>
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
                        <td>{{ $movement->type === 'in' ? 'Entrada' : 'Salida' }}</td>
                        <td>{{ $movement->quantity }}</td>
                        <td>{{ $movement->reason }}</td>
                        <td>{{ $movement->user->name ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">Sin movimientos todavía.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </figure>
@endsection
