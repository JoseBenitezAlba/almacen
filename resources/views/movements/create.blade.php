@extends('layouts.app')

@section('title', 'Registrar movimiento')

@section('content')
    <div class="page-head">
        <div>
            <h1>Registrar movimiento</h1>
            <p>{{ $product->name }} · {{ $product->code }}</p>
        </div>
        <div>
            @include('partials.stock-badge', ['product' => $product])
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            @include('partials.errors')

            <form method="POST" action="{{ route('movements.store', $product) }}">
                @csrf

                <div class="form-grid">
                    <div class="field">
                        <label for="type">Tipo</label>
                        <select id="type" name="type" required>
                            <option value="in" @selected(old('type') === 'in')>Entrada</option>
                            <option value="out" @selected(old('type') === 'out')>Salida</option>
                        </select>
                    </div>

                    <div class="field">
                        <label for="quantity">Cantidad ({{ $product->unit }})</label>
                        <input id="quantity" type="number" name="quantity" min="1" value="{{ old('quantity') }}" required>
                    </div>
                </div>

                <div class="field">
                    <label for="reason">Motivo</label>
                    <input id="reason" type="text" name="reason" value="{{ old('reason') }}" placeholder="Compra, ajuste de inventario, merma...">
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn">Registrar</button>
                    <a class="btn btn-ghost" href="{{ route('products.show', $product) }}">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
@endsection