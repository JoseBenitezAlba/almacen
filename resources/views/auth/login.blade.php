<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar sesión · Almacén</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">
</head>
<body>
    <main class="container" style="max-width: 440px; margin-top: 10vh;">
        <hgroup>
            <h1>Almacén</h1>
            <p>Inicia sesión para continuar</p>
        </hgroup>

        @if ($errors->any())
            <article>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </article>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <label>
                Correo electrónico
                <input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email">
            </label>

            <label>
                Contraseña
                <input type="password" name="password" required autocomplete="current-password">
            </label>

            <label>
                <input type="checkbox" name="remember" value="1">
                Mantener la sesión iniciada
            </label>

            <button type="submit">Entrar</button>
        </form>
    </main>
</body>
</html>