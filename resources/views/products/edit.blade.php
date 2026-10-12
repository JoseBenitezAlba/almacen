@extends('layouts.app')

@section('title', 'Editar producto')

@section('content')
    <div class="page-head">
        <div>
            <h1>Editar producto</h1>
            <p>{{ $product->name }} · {{ $product->code }}</p>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('products.update', $product) }}">
                @csrf
                @method('PUT')

                @include('products.form', ['product' => $product])

                <div class="form-actions">
                    <button type="submit" class="btn">Actualizar</button>
                    <a class="btn btn-ghost" href="{{ route('products.show', $product) }}">Cancelar</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card mt">
        <div class="card-body">
            <h2 style="font-size: 1.05rem;">Eliminar producto</h2>
            <p class="muted" style="margin: 4px 0 14px;">
                Solo se puede eliminar si no tiene movimientos registrados.
            </p>

            <form method="POST" action="{{ route('products.destroy', $product) }}"
                  onsubmit="return confirm('¿Eliminar este producto?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Eliminar producto</button>
            </form>
        </div>
    </div>
@endsection