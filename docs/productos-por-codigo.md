# Localizar productos por código

Primera fase. No modifica cómo se registran ventas ni movimientos, ni cambia los permisos.

## Qué añade

En Productos, entra en **Localizar por código**. Escribe el código completo que ya tiene el producto, por ejemplo `0012345678905`, y pulsa **Buscar producto**. Si existe, abre su ficha. El administrador puede continuar desde ahí a **Registrar movimiento**, con el formulario de siempre. El empleado puede consultar la ficha, pero no gana permiso para modificar stock.

Se reutiliza `products.code`, que ya era único. No hay migraciones nuevas, otro identificador ni productos creados automáticamente. Una búsqueda o una lectura de cámara nunca cambia existencias.

## Cámara, como ayuda opcional

El botón **Probar cámara** solo aparece si el navegador ofrece `BarcodeDetector`, un formato admitido y acceso a cámara en un contexto seguro: HTTPS o localhost. No se añade una dependencia externa. No funciona en todos los navegadores.

La lectura solo rellena el campo. Hay que revisarla y pulsar **Buscar producto**. Se para la cámara al leer, cerrar, enviar, salir de la página o dejarla en segundo plano. Si hay varios códigos visibles, no elige uno. Si falla o se deniega el permiso, la entrada escrita sigue disponible.

El código de barras o QR tiene que contener el mismo texto que `products.code`. Un QR con un enlace no se convierte en un producto. Si el catálogo usa códigos internos y el envase tiene un EAN distinto, esa relación no se inventa en esta fase.

**Pendiente:** probar la lectura con el móvil, navegador y etiquetas reales. Las pruebas automatizadas simulan la cámara y el detector; no demuestran que una etiqueta física se lea bien. Tampoco se ha probado un lector USB físico.

## Prueba manual

1. En un entorno de desarrollo, crea o edita un producto como administrador. Usa el código `0012345678905` y anota sus existencias. No uses datos reales para esta prueba.
2. Abre Productos > Localizar por código. Escribe ese código y comprueba que se abre la ficha correcta y se conservan sus ceros.
3. Prueba `00123` y un código inexistente. Debe avisar de que no existe, sin crear nada ni modificar stock.
4. Desde la ficha, registra una entrada de 2 unidades como administrador. Comprueba existencias e historial. La búsqueda por sí sola no debe haber registrado ningún movimiento.
5. Repite como empleado. Puede consultar, pero no registrar movimientos. Las ventas mantienen los permisos anteriores.
6. Desactiva JavaScript y repite la búsqueda escrita.
7. En HTTPS o localhost, usa un navegador compatible y una etiqueta con el código exacto. Prueba permitir, denegar y cerrar la cámara. Tras leer debe quedar el código para revisarlo, sin enviar ni registrar stock. Comprueba que el indicador de cámara se apaga al cerrar o cambiar de pantalla.
8. Si abres la app desde el móvil por una dirección HTTP de la red local, la cámara puede no estar disponible. No desactives la seguridad del navegador: usa la búsqueda escrita o un entorno HTTPS de pruebas.

## Pruebas automatizadas

```sh
php artisan test
```

`ProductLookupTest` cubre sesión, ruta, código completo con ceros, ausencia de coincidencia parcial, validación, salida HTML escapada, stock intacto al buscar, permisos y continuación al movimiento existente. Se conservan las pruebas de ventas y stock anteriores.

Para seguir el código: `routes/web.php` dirige a `ProductLookupController`; este busca por igualdad y abre la ficha existente. `products/lookup.blade.php` funciona como un formulario GET normal. `public/js/product-code.js` añade la cámara si está disponible, sin ser requisito de la búsqueda.
