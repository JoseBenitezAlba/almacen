@extends('layouts.app')

@section('title', 'Nuevo producto')

@section('content')
    <h1>Nuevo producto</h1>

    <form method="POST" action="{{ route('products.store') }}">
        @csrf

        @include('products.form')

        <button type="submit">Guardar</button>
        <a href="{{ route('products.index') }}" role="button" class="secondary">Cancelar</a>
    </form>
@endsection