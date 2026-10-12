@extends('layouts.app')

@section('title', 'Categorías')

@section('content')
    <div class="page-head">
        <div>
            <h1>Categorías</h1>
            <p>Agrupa tus productos para encontrarlos antes</p>
        </div>
    </div>

    @include('partials.errors')

    <div class="card">
        <form method="POST" action="{{ route('categories.store') }}" class="toolbar">
            @csrf
            <input type="text" name="name" value="{{ old('name') }}" placeholder="Nueva categoría" required>
            <button type="submit" class="btn">Añadir</button>
        </form>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Productos</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categories as $category)
                        <tr>
                            <td><strong>{{ $category->name }}</strong></td>
                            <td>{{ $category->products_count }}</td>
                            <td>
                                <div class="row-actions">
                                    <form method="POST" action="{{ route('categories.destroy', $category) }}" class="inline-form"
                                          onsubmit="return confirm('¿Eliminar la categoría {{ $category->name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="empty">Todavía no hay categorías. Crea la primera arriba.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection