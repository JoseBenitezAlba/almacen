@php($product = $product ?? null)

@if ($errors->any())
    <article>
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
        <input type="text" name="category" value="{{ old('category', $product->category ?? '') }}">
    </label>

    <label>
        Ubicación
        <input type="text" name="location" value="{{ old('location', $product->location ?? '') }}">
    </label>
</div>

<div class="grid">
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