@if ((int) $product->stock === 0)
    <span class="badge badge-danger">Sin stock</span>
@elseif ($product->isLowStock())
    <span class="badge badge-warn">{{ $product->stock }} {{ $product->unit }} · bajo</span>
@else
    <span class="badge badge-ok">{{ $product->stock }} {{ $product->unit }}</span>
@endif