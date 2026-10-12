@extends('layouts.app')

@section('title', 'Categorías')

@section('content')
    <hgroup>
        <h1>Categorías</h1>
        <p>Agrupa tus productos para encontrarlos antes</p>
    </hgroup>

    @if ($errors->any())
        <article class="flash flash-error">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </article>
    @endif

    <form method="POST" action="{{ route('categories.store') }}" role="group">
        @csrf
        <input type="text" name="name" value="{{ old('name') }}" placeholder="Nueva categoría" required>
        <button type="submit">Añadir</button>
    </form>

    <figure>
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
                        <td>{{ $category->name }}</td>
                        <td>{{ $category->products_count }}</td>
                        <td>
                            <form method="POST" action="{{ route('categories.destroy', $category) }}"
                                  onsubmit="return confirm('¿Eliminar la categoría {{ $category->name }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="contrast outline" style="margin: 0; padding: .2rem .7rem;">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">Todavía no hay categorías. Crea la primera arriba.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </figure>
@endsection