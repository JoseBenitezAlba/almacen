@extends('layouts.app')

@section('title', 'Productos')

@section('content')
    <div class="page-head">
        <div>
            <h1>Productos</h1>
            <p>Catálogo y existencias del almacén</p>
        </div>

        @if (auth()->user()->isAdmin())
            <div class="actions">
                <a class="btn" href="{{ route('products.create') }}">+ Nuevo producto</a>
            </div>
        @endif
    </div>

    <div class="card">
        <form method="GET" action="{{ route('products.index') }}" class="toolbar">
            <input type="search" name="q" value="{{ $search }}" placeholder="Buscar por nombre, código o categoría">
            <button type="submit" class="btn">Buscar</button>
        </form>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Producto</th>
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
                            <td>
                                <a href="{{ route('products.show', $product) }}"><strong>{{ $product->name }}</strong></a>
                                @if ($product->category)
                                    <div class="muted" style="font-size: .8rem;">{{ $product->category->name }}</div>
                                @endif
                            </td>
                            <td>{{ $product->location ?? '—' }}</td>
                            <td>{{ $product->price_formatted }}</td>
                            <td>@include('partials.stock-badge', ['product' => $product])</td>
                            <td>
                                @if (auth()->user()->isAdmin())
                                    <div class="row-actions">
                                        <a class="btn btn-ghost btn-sm" href="{{ route('movements.create', $product) }}">Movimiento</a>
                                        <a class="btn btn-ghost btn-sm" href="{{ route('products.edit', $product) }}">Editar</a>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="empty">No hay productos todavía.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @include('partials.pager', ['paginator' => $products])
@endsection