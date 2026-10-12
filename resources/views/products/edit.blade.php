@extends('layouts.app')

@section('title', 'Editar producto')

@section('content')
    <h1>Editar producto</h1>

    <form method="POST" action="{{ route('products.update', $product) }}">
        @csrf
        @method('PUT')

        @include('products.form', ['product' => $product])

        <button type="submit">Actualizar</button>
        <a href="{{ route('products.index') }}" role="button" class="secondary">Cancelar</a>
    </form>

    <hr>

    <form method="POST" action="{{ route('products.destroy', $product) }}"
          onsubmit="return confirm('¿Eliminar este producto y todos sus movimientos?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="contrast outline">Eliminar producto</button>
    </form>
@endsection