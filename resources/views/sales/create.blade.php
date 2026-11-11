@extends('layouts.app')

@section('title', 'Registrar venta')

@section('content')
    <div class="page-head">
        <div>
            <h1>Registrar venta</h1>
            <p>Elige los productos y las cantidades; el stock se descuenta al guardar</p>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            @include('partials.errors')

            <form method="POST" action="{{ route('sales.store') }}">
                @csrf

                <div class="field">
                    <label for="customer_name">Cliente (opcional)</label>
                    <input id="customer_name" type="text" name="customer_name"
                           value="{{ old('customer_name') }}" placeholder="Venta en tienda">
                </div>

                <h2 style="font-size: 1.05rem; margin-bottom: 10px;">Productos</h2>

                <div id="lines"></div>

                <button type="button" id="addLine" class="btn btn-ghost btn-sm">+ Añadir línea</button>

                <div class="sale-total">
                    <span>Total</span>
                    <span id="grandTotal">0,00 €</span>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn">Registrar venta</button>
                    <a class="btn btn-ghost" href="{{ route('sales.index') }}">Cancelar</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Plantilla de una línea: el JavaScript la copia y cambia __i__ por un número --}}
    <template id="lineTemplate">
        <div class="line-row">
            <select name="items[__i__][product_id]" required>
                <option value="">Elige un producto</option>
                @foreach ($products as $product)
                    <option value="{{ $product->id }}" data-price="{{ $product->price_cents }}">
                        {{ $product->name }} ({{ $product->code }}) · {{ $product->stock }} {{ $product->unit }} · {{ $product->price_formatted }}
                    </option>
                @endforeach
            </select>
            <input type="number" name="items[__i__][quantity]" min="1" value="1" required>
            <span class="line-total">0,00 €</span>
            <button type="button" class="btn btn-danger btn-sm remove">Quitar</button>
        </div>
    </template>
@endsection

@push('scripts')
    <style>
        .line-row { display: grid; grid-template-columns: 1fr 110px 110px auto; gap: 10px; align-items: center; margin-bottom: 10px; }
        .line-total { text-align: right; font-weight: 600; }
        .sale-total { display: flex; justify-content: space-between; align-items: center; padding: 14px 0 4px; margin-top: 8px; border-top: 1px solid var(--border); font-size: 1.1rem; font-weight: 700; }

        @media (max-width: 860px) {
            .line-row { grid-template-columns: 1fr 90px; }
            .line-total { text-align: left; }
        }
    </style>
    <script>
        const lines = document.getElementById('lines');
        const template = document.getElementById('lineTemplate');
        const oldItems = @json(old('items', []));
        let index = 0;

        // Solo para mostrar: el servidor vuelve a calcular los precios desde la base de datos
        function euros(cents) {
            return (cents / 100).toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' €';
        }

        function recalc() {
            let total = 0;

            lines.querySelectorAll('.line-row').forEach(function (row) {
                const option = row.querySelector('select').selectedOptions[0];
                const quantity = parseInt(row.querySelector('input').value, 10) || 0;
                const price = option ? parseInt(option.dataset.price || 0, 10) : 0;
                const subtotal = price * quantity;

                row.querySelector('.line-total').textContent = euros(subtotal);
                total += subtotal;
            });

            document.getElementById('grandTotal').textContent = euros(total);
        }

        function addLine(item) {
            item = item || {};

            const wrapper = document.createElement('div');
            wrapper.innerHTML = template.innerHTML.replaceAll('__i__', index++).trim();
            const row = wrapper.firstElementChild;

            if (item.product_id) row.querySelector('select').value = item.product_id;
            if (item.quantity) row.querySelector('input').value = item.quantity;

            lines.appendChild(row);
            recalc();
        }

        document.getElementById('addLine').addEventListener('click', function () { addLine(); });
        lines.addEventListener('input', recalc);
        lines.addEventListener('change', recalc);

        lines.addEventListener('click', function (event) {
            if (event.target.classList.contains('remove') && lines.children.length > 1) {
                event.target.closest('.line-row').remove();
                recalc();
            }
        });

        // Si el servidor devolvió errores, se recuperan las líneas que había escrito
        const previous = Object.values(oldItems);
        if (previous.length) {
            previous.forEach(function (item) { addLine(item); });
        } else {
            addLine();
        }
    </script>
@endpush
