@php($product = $product ?? null)

@if ($errors->any())
    <article class="flash flash-error">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </article>
@endif

<label>
    Código
    <input type="text" name="code" value="{{ old('code', $product->code ?? '') }}" required>
</label>

<label>
    Nombre
    <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}" required>
</label>

<div class="grid">
    <label>
        Categoría
        <select name="category_id">
            <option value="">Sin categoría</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}"
                    @selected((string) old('category_id', $product->category_id ?? '') === (string) $category->id)>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
    </label>

    <label>
        Ubicación
        <input type="text" name="location" value="{{ old('location', $product->location ?? '') }}">
    </label>
</div>

<div class="grid">
    <label>
        Precio de venta (€)
        <input type="text" name="price" inputmode="decimal" placeholder="0,00"
               value="{{ old('price', $product ? number_format($product->price_cents / 100, 2, ',', '') : '0,00') }}" required>
    </label>

    <label>
        Unidad
        <input type="text" name="unit" value="{{ old('unit', $product->unit ?? 'ud') }}" required>
    </label>

    <label>
        Stock mínimo
        <input type="number" name="min_stock" min="0" value="{{ old('min_stock', $product->min_stock ?? 0) }}" required>
    </label>
</div>

<label>
    Descripción
    <textarea name="description" rows="3">{{ old('description', $product->description ?? '') }}</textarea>
</label>