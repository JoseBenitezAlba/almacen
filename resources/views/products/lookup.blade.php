@extends('layouts.app')

@section('title', 'Localizar por código')

@section('content')
    <div class="page-head">
        <div>
            <h1>Localizar por código</h1>
            <p>Encuentra el producto antes de registrar una entrada o salida.</p>
        </div>
        <a class="btn btn-ghost" href="{{ route('products.index') }}">Volver al catálogo</a>
    </div>

    <div class="card code-lookup">
        <div class="card-body">
            @include('partials.errors')
            <form method="GET" action="{{ route('products.lookup') }}" id="code-lookup-form">
                <div class="field">
                    <label for="lookup-code">Código exacto del producto</label>
                    <input id="lookup-code" type="text" name="code" maxlength="50"
                           value="{{ old('code', $code) }}" required autofocus autocomplete="off"
                           spellcheck="false" autocapitalize="off" aria-describedby="code-help">
                    <small id="code-help">Escribe el código guardado en el catálogo. Se conservan los ceros iniciales. También puedes usar un lector que escriba como un teclado.</small>
                </div>
                <div class="form-actions">
                    <button class="btn" type="submit">Buscar producto</button>
                    <button class="btn btn-ghost" type="button" id="start-code-camera" hidden>Probar cámara</button>
                </div>
            </form>

            @if ($notFound)
                <div class="flash flash-error mt" role="alert">
                    No hay ningún producto con el código <strong>{{ $code }}</strong>.
                    Comprueba el código del catálogo. No se ha creado ningún producto ni cambiado el stock.
                </div>
            @endif

            <div class="camera-section mt">
                <h2>Cámara opcional</h2>
                <p class="muted">El código de barras o QR debe contener exactamente el código del catálogo, no una dirección web. La cámara solo rellena el campo: revisa el resultado y pulsa Buscar producto.</p>
                <p id="camera-status" class="mt" role="status" aria-live="polite">Puedes completar todo el flujo escribiendo el código, sin cámara ni permisos.</p>
                <div id="code-camera-panel" class="mt" hidden>
                    <video id="code-camera-video" autoplay muted playsinline aria-label="Vista previa de la cámara"></video>
                    <button class="btn btn-ghost mt" type="button" id="stop-code-camera">Cerrar cámara</button>
                </div>
                <noscript><p class="mt">La cámara necesita JavaScript. La búsqueda escrita sigue funcionando.</p></noscript>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/product-code.js') }}" defer></script>
@endpush
