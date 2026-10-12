@extends('layouts.app')

@section('title', 'Registrar movimiento')

@section('content')
    <hgroup>
        <h1>Registrar movimiento</h1>
        <p>
            {{ $product->name }} ({{ $product->code }}) ·
            Stock actual: {{ $product->stock }} {{ $product->unit }}
        </p>
    </hgroup>

    @if ($errors->any())
        <article>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </article>
    @endif

    <form method="POST" action="{{ route('movements.store', $product) }}">
        @csrf

        <label>
            Tipo
            <select name="type" required>
                <option value="in" @selected(old('type') === 'in')>Entrada</option>
                <option value="out" @selected(old('type') === 'out')>Salida</option>
            </select>
        </label>

        <label>
            Cantidad ({{ $product->unit }})
            <input type="number" name="quantity" min="1" value="{{ old('quantity') }}" required>
        </label>

        <label>
            Motivo
            <input type="text" name="reason" value="{{ old('reason') }}" placeholder="Compra, venta, ajuste de inventario...">
        </label>

        <button type="submit">Registrar</button>
        <a href="{{ route('products.show', $product) }}" role="button" class="secondary">Cancelar</a>
    </form>
@endsection