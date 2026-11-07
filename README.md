# GTS Fashion

Tienda PHP + MySQL con catálogo, carrito, compras contra entrega, perfiles de cliente y panel administrativo.

## Requisitos

- PHP 8.1 o superior (extensiones mysqli, fileinfo y mbstring).
- MariaDB 10.5+ (recomendado MariaDB 10.11) o MySQL compatible.
- Apache con mod_rewrite opcional y permisos de escritura en `uploads/`.

## Instalación limpia

1. Crea una base de datos llamada `gts_fashion` con cotejamiento `utf8mb4_unicode_ci`.
2. Importa `gts_fashion.sql` desde phpMyAdmin.
3. Ejecuta una sola vez `migracion_completa.sql` sobre esa base. Este archivo agrega los campos requeridos por registro, perfiles, inicio de sesión y datos de entrega. Está preparado para MariaDB y utiliza `IF NOT EXISTS`.
4. Copia `config.local.example.php` como `config.local.php` y configura host, usuario, contraseña y nombre real de la base.
5. Sube el proyecto conservando las carpetas `imagenes/`, `uploads/` y los archivos CSS.
6. Da permisos de escritura al servidor web sobre `uploads/` y sus subcarpetas.
7. Configura `setup_key` en `config.local.php` con una clave larga y aleatoria. Abre `crear_admin.php` para crear la cuenta administradora inicial.
8. Después de crear el administrador, elimina `crear_admin.php` del servidor.
9. Inicia sesión y comprueba catálogo, registro, carrito, compra de prueba, pedidos, categorías, clientes e informes.

## Actualización de una instalación existente

1. Haz una copia de seguridad completa de la base de datos y de las carpetas de imágenes.
2. Actualiza los archivos del proyecto sin sobrescribir `config.local.php`, `uploads/` ni `imagenes/`.
3. Ejecuta `migracion_completa.sql` una sola vez o vuelve a ejecutarla si necesitas verificar columnas e índices faltantes.
4. Comprueba las credenciales y la versión de PHP del hosting.
5. Realiza una compra de prueba y verifica que el pedido, sus detalles y la reducción de inventario se guarden correctamente.

## Configuración local

El archivo `config.local.php` no debe subirse al repositorio. Ejemplo:

```php
<?php
return array(
    'host' => 'localhost',
    'usuario' => 'USUARIO_MYSQL',
    'clave' => 'CONTRASENA_MYSQL',
    'base' => 'gts_fashion',
    'setup_key' => 'REEMPLAZA_POR_UN_SECRETO_LARGO_Y_ALEATORIO'
);
```

En hosting compartido, el proveedor suele entregar un host MySQL distinto de `localhost`, además de un nombre de base y usuario con prefijo.

## Funcionalidades administrativas

- Productos: crear, consultar, editar y eliminar (se conserva la integridad referencial).
- Categorías: crear, consultar, editar y eliminar solo si no tienen productos.
- Clientes: consultar, editar y eliminar solo si no tienen pedidos asociados.
- Administradores: crear, consultar, editar y eliminar sin permitir borrar la sesión actual ni el último administrador.
- Pedidos: consultar, cambiar estado y revisar sus líneas de detalle.
- Informes: ventas por rango de fechas y exportación CSV.

## Seguridad y operación

- Contraseñas guardadas con `password_hash()` y verificadas con `password_verify()`.
- Consultas preparadas para datos de formularios.
- Tokens CSRF en formularios principales.
- Sesiones con cookies HttpOnly, SameSite y regeneración del ID al autenticar.
- El stock se vuelve a comprobar dentro de una transacción al confirmar una compra.
- No publiques credenciales de MySQL ni la clave de instalación.
- No uses datos reales de clientes en pruebas o demostraciones.

## Comprobación antes de entregar

- [ ] Importación SQL sin errores.
- [ ] Registro e inicio/cierre de sesión de cliente.
- [ ] Inicio/cierre de sesión administrativo.
- [ ] Acceso directo a páginas privadas bloqueado sin sesión.
- [ ] CRUD de productos y categorías.
- [ ] Edición y eliminación controlada de clientes y administradores.
- [ ] Compra de prueba: pedido, detalle y stock coherentes.
- [ ] Cambio de estado y consulta de detalle del pedido.
- [ ] Informe por fechas y exportación CSV.
- [ ] Prueba en móvil y escritorio.
- [ ] Sitio y conexión MySQL verificados en el hosting.

**Importante:** las pruebas de producción deben ejecutarse en el hosting real. La publicación de archivos en GitHub por sí sola no demuestra que la tienda esté funcionando en producción.
