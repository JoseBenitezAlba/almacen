@extends('layouts.app')

@section('title', 'Movimientos')

@section('content')
    <hgroup>
        <h1>Movimientos</h1>
        <p>Historial de entradas y salidas de todo el almacén</p>
    </hgroup>

    <figure>
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Producto</th>
                    <th>Tipo</th>
                    <th>Cantidad</th>
                    <th>Motivo</th>
                    <th>Usuario</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($movements as $movement)
                    <tr>
                        <td>{{ $movement->created_at->format('d/m/Y H:i') }}</td>
                        <td><a href="{{ route('products.show', $movement->product) }}">{{ $movement->product->name }}</a></td>
                        <td class="{{ $movement->type === 'in' ? 'mov-in' : 'mov-out' }}">
                            {{ $movement->type === 'in' ? 'Entrada' : 'Salida' }}
                        </td>
                        <td>{{ $movement->quantity }}</td>
                        <td>{{ $movement->reason }}</td>
                        <td>{{ $movement->user->name ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">Todavía no hay movimientos.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </figure>

    {{ $movements->links('pagination::simple-default') }}
@endsection