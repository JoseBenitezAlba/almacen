@extends('layouts.app')

@section('title', 'Nuevo producto')

@section('content')
    <div class="page-head">
        <div>
            <h1>Nuevo producto</h1>
            <p>Añade una referencia al catálogo</p>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('products.store') }}">
                @csrf

                @include('products.form')

                <div class="form-actions">
                    <button type="submit" class="btn">Guardar</button>
                    <a class="btn btn-ghost" href="{{ route('products.index') }}">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
@endsection