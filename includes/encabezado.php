<?php
$titulo=isset($titulo)?$titulo:'GTS Fashion';$es_admin=!empty($_SESSION['administrador_id']);$es_cliente=!empty($_SESSION['cliente_id']);
$carrito_total=0;if(!empty($_SESSION['carrito'])&&is_array($_SESSION['carrito']))foreach($_SESSION['carrito'] as $cantidad)$carrito_total+=(int)$cantidad;
$admin_usuario=$_SESSION['administrador_usuario']??'Administrador';
$admin_pagina=basename($_SERVER['PHP_SELF']??'');
?>
<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="theme-color" content="#fff0f8"><title><?php echo e($titulo);?> | GTS Fashion</title><link rel="stylesheet" href="styles.css"></head>
<body class="<?php echo $es_admin?'vista-gestion':'vista-tienda';?>">
<?php if($es_admin):?>
<aside class="panel-lateral">
<a class="panel-simbolo" href="admin_inicio.php" aria-label="GTS Fashion inicio">GTS</a>
<nav class="menu-gestion" aria-label="Menú de gestión">
<a class="menu-inicio <?php echo $admin_pagina==='admin_inicio.php'?'activo':'';?>" href="admin_inicio.php">Inicio</a>
<div class="grupo-menu">Sitio web</div>
<a class="submenu-gestion <?php echo $admin_pagina==='paginas.php'?'activo':'';?>" href="paginas.php">Páginas</a>
<a class="submenu-gestion <?php echo $admin_pagina==='estilos_paginas.php'?'activo':'';?>" href="estilos_paginas.php">Estilos de páginas</a>
<a class="submenu-gestion <?php echo $admin_pagina==='recursos.php'?'activo':'';?>" href="recursos.php">Recursos</a>
<a class="menu-seccion <?php echo in_array($admin_pagina,array('mostrar_datos.php','guardar.php','actualizar.php'))?'activo':'';?>" href="mostrar_datos.php">Productos y servicios</a>
<a class="submenu-gestion" href="mostrar_datos.php">Inventario</a><a class="submenu-gestion" href="guardar.php">Añadir producto</a>
<a class="submenu-gestion <?php echo $admin_pagina==='clientes.php'?'activo':'';?>" href="clientes.php">Clientes</a>
<a class="menu-seccion <?php echo $admin_pagina==='pedidos.php'?'activo':'';?>" href="pedidos.php">Pedidos</a>
<a class="menu-seccion <?php echo $admin_pagina==='informes.php'?'activo':'';?>" href="informes.php">Ventas e informes</a>
<a class="menu-seccion" href="index.php">Ver tienda ↗</a>
</nav>
<div class="panel-pie"><span class="admin-avatar"><?php echo e(mb_strtoupper(mb_substr($admin_usuario,0,1)));?></span><span><?php echo e($admin_usuario);?></span><a href="logout.php" title="Cerrar sesión" aria-label="Cerrar sesión">Salir</a></div>
</aside><div class="zona-gestion">
<?php else:?>
<header class="site-header"><nav class="nav-principal" aria-label="Navegación principal"><a href="index.php">Tienda</a><a href="index.php#catalogo">Catálogo</a><a href="index.php#acerca">Acerca de</a><a href="index.php#contacto">Contacto</a></nav>
<a class="marca" href="index.php">GTS Fashion</a><div class="acciones-header">
<?php if($es_cliente):?><a href="perfil.php">Mi perfil</a><a href="mis_pedidos.php">Mis pedidos</a><a href="logout.php">Salir</a><?php else:?><a href="login.php">Iniciar sesión</a><?php endif;?>
<a class="icono-social" href="#contacto" aria-label="Instagram">◎</a><a class="icono-social" href="#contacto" aria-label="Red social">♥</a><a class="carrito-resumen" href="carrito.php" aria-label="Carrito de compras">♧ <span><?php echo $carrito_total;?></span></a></div></header>
<?php endif;?>
