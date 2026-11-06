<?php
$titulo = isset($titulo) ? $titulo : 'GTS Fashion';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#ffffff">
    <title><?php echo e($titulo); ?> | GTS Fashion</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<header class="site-header">
    <nav class="nav-principal" aria-label="Navegación principal">
        <a href="index.php">Tienda</a>
        <a href="index.php#catalogo">Catálogo</a>
        <a href="index.php#acerca">Acerca de</a>
        <a href="index.php#contacto">Contacto</a>
    </nav>
    <a class="marca" href="index.php" aria-label="GTS Fashion, inicio">GTS Fashion</a>
    <div class="acciones-header">
        <?php if (!empty($_SESSION['administrador_id'])): ?>
            <a href="mostrar_datos.php">Administración</a>
            <a href="logout.php">Salir</a>
        <?php else: ?>
            <a href="login.php">Iniciar sesión</a>
        <?php endif; ?>
        <a class="icono-social" href="#contacto" aria-label="Instagram"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3.5" y="3.5" width="17" height="17" rx="5"></rect><circle cx="12" cy="12" r="4"></circle><circle class="punto" cx="17.5" cy="6.8" r="1"></circle></svg></a>
        <a class="icono-social" href="#contacto" aria-label="Twitter"><svg viewBox="0 0 24 24" aria-hidden="true"><path class="relleno" d="M21.2 6.1c-.7.3-1.4.5-2.2.6a3.7 3.7 0 0 0 1.7-2.1 7.4 7.4 0 0 1-2.4.9 3.7 3.7 0 0 0-6.4 2.5c0 .3 0 .6.1.8a10.5 10.5 0 0 1-7.6-3.9 3.7 3.7 0 0 0 1.1 4.9 3.7 3.7 0 0 1-1.7-.5v.1c0 1.8 1.3 3.3 3 3.6-.3.1-.7.2-1 .2-.2 0-.5 0-.7-.1.5 1.5 1.9 2.6 3.5 2.6A7.4 7.4 0 0 1 3.2 17c-.3 0-.6 0-.9-.1a10.5 10.5 0 0 0 5.7 1.7c6.8 0 10.5-5.7 10.5-10.5v-.5c.8-.4 1.4-.9 2-1.5Z"></path></svg></a>
        <a class="carrito-resumen" href="#carrito" aria-label="Carrito de compras"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2.5 3.5h2l2.2 12.1a2 2 0 0 0 2 1.6h9.5a2 2 0 0 0 1.9-1.5L22 8H6"></path><circle cx="9" cy="20" r="1"></circle><circle cx="18" cy="20" r="1"></circle></svg><span>0</span></a>
    </div>
</header>
