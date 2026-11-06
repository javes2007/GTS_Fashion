<?php
$titulo = isset($titulo) ? $titulo : 'GTS Fashion';
$pagina = isset($pagina) ? $pagina : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($titulo); ?> | GTS Fashion</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body>
<header class="encabezado">
    <a class="marca" href="index.php">GTS Fashion</a>
    <nav>
        <a href="index.php">Catálogo</a>
        <?php if (!empty($_SESSION['administrador_id'])): ?>
            <a href="mostrar_datos.php">Inventario</a>
            <a href="informes.php">Informes</a>
            <a href="logout.php">Cerrar sesión</a>
        <?php else: ?>
            <a href="login.php">Administración</a>
        <?php endif; ?>
    </nav>
</header>
