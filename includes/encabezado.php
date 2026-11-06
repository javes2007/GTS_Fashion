<?php
$titulo=isset($titulo)?$titulo:'GTS Fashion';$es_admin=!empty($_SESSION['administrador_id']);$es_cliente=!empty($_SESSION['cliente_id']);
$carrito_total=0;if(!empty($_SESSION['carrito'])&&is_array($_SESSION['carrito']))foreach($_SESSION['carrito'] as $cantidad)$carrito_total+=(int)$cantidad;
?>
<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="theme-color" content="#fff0f8"><title><?php echo e($titulo);?> | GTS Fashion</title><link rel="stylesheet" href="styles.css"></head>
<body class="<?php echo $es_admin?'vista-gestion':'vista-tienda';?>">
<?php if($es_admin):?>
<aside class="panel-lateral"><a class="panel-simbolo" href="mostrar_datos.php">GTS</a><nav class="menu-gestion" aria-label="Menú de gestión">
<a href="mostrar_datos.php">Inicio</a><span class="grupo-menu">Sitio web</span><a href="mostrar_datos.php">Productos</a><a href="guardar.php">Agregar producto</a><a href="informes.php">Informes de ventas</a><a href="pedidos.php">Pedidos</a><a href="index.php">Ver tienda</a><a href="logout.php">Cerrar sesión</a></nav><div class="panel-pie"><?php echo e($_SESSION['administrador_usuario']??'');?></div></aside><div class="zona-gestion">
<?php else:?>
<header class="site-header"><nav class="nav-principal" aria-label="Navegación principal"><a href="index.php">Tienda</a><a href="index.php#catalogo">Catálogo</a><a href="index.php#acerca">Acerca de</a><a href="index.php#contacto">Contacto</a></nav>
<a class="marca" href="index.php">GTS Fashion</a><div class="acciones-header">
<?php if($es_cliente):?><a href="perfil.php">Mi perfil</a><a href="mis_pedidos.php">Mis pedidos</a><a href="logout.php">Salir</a><?php else:?><a href="login.php">Iniciar sesión</a><?php endif;?>
<a class="icono-social" href="#contacto" aria-label="Instagram">◎</a><a class="icono-social" href="#contacto" aria-label="Red social">♥</a><a class="carrito-resumen" href="carrito.php" aria-label="Carrito de compras">♧ <span><?php echo $carrito_total;?></span></a></div></header>
<?php endif;?>
