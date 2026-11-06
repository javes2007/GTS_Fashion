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
        <a class="icono-social" href="#contacto" aria-label="Instagram">◎</a>
        <a class="icono-social" href="#contacto" aria-label="Twitter">♥</a>
        <a class="carrito-resumen" href="#carrito" aria-label="Carrito de compras"><span aria-hidden="true">🛒</span><span>0</span></a>
    </div>
</header>
