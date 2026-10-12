@extends('layouts.app')

@section('title', 'Productos')

@section('content')
    <hgroup>
        <h1>Productos</h1>
        <p>Listado de productos del almacén</p>
    </hgroup>

    <form method="GET" action="{{ route('products.index') }}" role="search">
        <input type="search" name="q" value="{{ $search }}" placeholder="Buscar por nombre, código o categoría">
        <button type="submit">Buscar</button>
    </form>

    @if (auth()->user()->isAdmin())
        <p>
            <a href="{{ route('products.create') }}" role="button">Nuevo producto</a>
        </p>
    @endif

    <figure>
        <table>
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Ubicación</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                    <tr>
                        <td>{{ $product->code }}</td>
                        <td><a href="{{ route('products.show', $product) }}">{{ $product->name }}</a></td>
                        <td>{{ $product->category?->name }}</td>
                        <td>{{ $product->location }}</td>
                        <td>{{ $product->price_formatted }}</td>
                        <td>
                            {{ $product->stock }} {{ $product->unit }}
                            @if ($product->isLowStock())
                                <mark>Stock bajo</mark>
                            @endif
                        </td>
                        <td>
                            @if (auth()->user()->isAdmin())
                                <a href="{{ route('movements.create', $product) }}">Movimiento</a>
                                ·
                                <a href="{{ route('products.edit', $product) }}">Editar</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">No hay productos todavía.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </figure>

    {{ $products->links('pagination::simple-default') }}
@endsection