<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Almacén')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">
    <style>
        .mov-in  { color: #2e7d32; font-weight: 600; }
        .mov-out { color: #c62828; font-weight: 600; }
        .stat    { font-size: 2.2rem; font-weight: 700; margin: 0; }
        .flash   { border-left: 4px solid #2e7d32; }
        .flash-error { border-left-color: #c62828; }
        .logout  { margin: 0; }
        .logout button { margin: 0; padding: .3rem .8rem; }
    </style>
</head>
<body>
    <header class="container">
        <nav>
            <ul>
                <li><strong>Almacén</strong></li>
            </ul>
            <ul>
                <li>
                    <a href="{{ route('home') }}" @if (request()->routeIs('home')) aria-current="page" @endif>Inicio</a>
                </li>
                <li>
                    <a href="{{ route('products.index') }}" @if (request()->routeIs('products.*')) aria-current="page" @endif>Productos</a>
                </li>
                @if (auth()->user()->isAdmin())
                    <li>
                        <a href="{{ route('categories.index') }}" @if (request()->routeIs('categories.*')) aria-current="page" @endif>Categorías</a>
                    </li>
                @endif
                <li>
                    <a href="{{ route('movements.index') }}" @if (request()->routeIs('movements.*')) aria-current="page" @endif>Movimientos</a>
                </li>
                <li>
                    <small>{{ auth()->user()->name }} ({{ auth()->user()->isAdmin() ? 'Administrador' : 'Empleado' }})</small>
                </li>
                <li>
                    <form method="POST" action="{{ route('logout') }}" class="logout">
                        @csrf
                        <button type="submit" class="secondary outline">Salir</button>
                    </form>
                </li>
            </ul>
        </nav>
    </header>

    <main class="container">
        @if (session('success'))
            <article class="flash">{{ session('success') }}</article>
        @endif

        @if (session('error'))
            <article class="flash flash-error">{{ session('error') }}</article>
        @endif

        @yield('content')
    </main>
</body>
</html>