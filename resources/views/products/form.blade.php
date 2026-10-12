@php($product = $product ?? null)

@include('partials.errors')

<div class="form-grid">
    <div class="field">
        <label for="code">Código</label>
        <input id="code" type="text" name="code" value="{{ old('code', $product->code ?? '') }}" required>
        <small>Código interno único del producto</small>
    </div>

    <div class="field">
        <label for="name">Nombre</label>
        <input id="name" type="text" name="name" value="{{ old('name', $product->name ?? '') }}" required>
    </div>
</div>

<div class="form-grid">
    <div class="field">
        <label for="category_id">Categoría</label>
        <select id="category_id" name="category_id">
            <option value="">Sin categoría</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}"
                    @selected((string) old('category_id', $product->category_id ?? '') === (string) $category->id)>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="field">
        <label for="location">Ubicación</label>
        <input id="location" type="text" name="location" value="{{ old('location', $product->location ?? '') }}">
    </div>
</div>

<div class="form-grid">
    <div class="field">
        <label for="price">Precio de venta (€)</label>
        <input id="price" type="text" name="price" inputmode="decimal" placeholder="0,00"
               value="{{ old('price', $product ? number_format($product->price_cents / 100, 2, ',', '') : '0,00') }}" required>
    </div>

    <div class="field">
        <label for="unit">Unidad</label>
        <input id="unit" type="text" name="unit" value="{{ old('unit', $product->unit ?? 'ud') }}" required>
    </div>

    <div class="field">
        <label for="min_stock">Stock mínimo</label>
        <input id="min_stock" type="number" name="min_stock" min="0" value="{{ old('min_stock', $product->min_stock ?? 0) }}" required>
        <small>Por debajo de esta cantidad se avisa de stock bajo</small>
    </div>
</div>

<div class="field">
    <label for="description">Descripción</label>
    <textarea id="description" name="description" rows="3">{{ old('description', $product->description ?? '') }}</textarea>
</div>