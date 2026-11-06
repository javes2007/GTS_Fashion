<?php
require_once __DIR__.'/verificar_sesion.php';
$db=conexion();
function total_tabla($db,$tabla){$permitidas=array('productos','pedidos','clientes');if(!in_array($tabla,$permitidas,true))return 0;$r=$db->query("SELECT COUNT(*) AS total FROM $tabla");return $r?(int)$r->fetch_assoc()['total']:0;}
$total_productos=total_tabla($db,'productos');$total_pedidos=total_tabla($db,'pedidos');$total_clientes=total_tabla($db,'clientes');
$ventas=0;$r=$db->query("SELECT COALESCE(SUM(total),0) AS total FROM pedidos WHERE estado <> 'Cancelado'");if($r)$ventas=(float)$r->fetch_assoc()['total'];
$pendientes=0;$r=$db->query("SELECT COUNT(*) AS total FROM pedidos WHERE estado='Pendiente'");if($r)$pendientes=(int)$r->fetch_assoc()['total'];
$admin_nombre=$_SESSION['administrador_usuario']??'Administrador';$titulo='Inicio';require __DIR__.'/includes/encabezado.php';
?>
<main class="contenido dashboard-admin">
<div class="dashboard-topline"><span class="sobrelinea">GTS FASHION · CENTRO DE CONTROL</span><a class="boton boton-secundario" href="index.php">Ver sitio web ↗</a></div>
<section class="bienvenida-admin"><div><p class="saludo-admin">Tu espacio de trabajo</p><h1>Te damos la bienvenida, <?php echo e($admin_nombre);?></h1><p>Desde aquí puedes personalizar tu tienda, organizar tus productos y gestionar tus ventas.</p></div><div class="monograma-admin">GTS</div></section>
<section class="configuracion-inicio"><div class="configuracion-cabecera"><div><span class="sobrelinea">EMPIEZA AQUÍ</span><h2>Personalizar la página de inicio</h2><p>Prepara cada parte de tu tienda para ofrecer una experiencia coherente.</p></div><a href="paginas.php">Editar sitio <span aria-hidden="true">↗</span></a></div>
<div class="progreso-config"><div><strong>Empezar</strong><span>0 / 7 completados</span></div><div class="barra-progreso"><span style="width:0%"></span></div></div>
<div class="tareas-config">
<a class="tarea-config" href="paginas.php"><span class="icono-tarea">▤</span><span><strong>Personaliza la página de inicio</strong><small>Organiza las páginas principales de tu tienda.</small></span><b>→</b></a>
<a class="tarea-config" href="estilos_paginas.php"><span class="icono-tarea">◈</span><span><strong>Personaliza el encabezado y el logotipo</strong><small>Define cómo se presenta la marca en tu sitio.</small></span><b>→</b></a>
<a class="tarea-config" href="estilos_paginas.php"><span class="icono-tarea">✳</span><span><strong>Personaliza el estilo de tu marca</strong><small>Consulta la paleta y los estilos visuales actuales.</small></span><b>→</b></a>
<a class="tarea-config" href="paginas.php"><span class="icono-tarea">☷</span><span><strong>Gestiona la navegación del sitio</strong><small>Revisa las secciones y sus enlaces.</small></span><b>→</b></a>
<a class="tarea-config" href="recursos.php"><span class="icono-tarea">▧</span><span><strong>Edita el contenido del sitio</strong><small>Administra imágenes y recursos de la tienda.</small></span><b>→</b></a>
<a class="tarea-config" href="recursos.php#seo"><span class="icono-tarea">⌕</span><span><strong>Modifica el SEO para los resultados de búsqueda</strong><small>Consulta las recomendaciones para posicionamiento.</small></span><b>→</b></a>
<a class="tarea-config" href="recursos.php#dominio"><span class="icono-tarea">◎</span><span><strong>Administra tu dominio</strong><small>Prepara la dirección pública de tu tienda.</small></span><b>→</b></a>
</div>
<div class="publicacion-resumen"><span class="estado-privado"><i></i> Privado</span><div><strong>GTS Fashion</strong><small>Tu tienda está disponible en el entorno donde la alojas.</small></div><a href="recursos.php#publicar">Publicar sitio ↗</a></div>
</section>
<section class="resumen-dashboard"><div class="titulo-seccion-dashboard"><div><span class="sobrelinea">RESUMEN DE TU NEGOCIO</span><h2>Tu tienda hoy</h2></div><a href="informes.php">Ver informes →</a></div>
<div class="metricas-dashboard">
<a class="metrica-dashboard" href="mostrar_datos.php"><span>Productos</span><strong><?php echo $total_productos;?></strong><small>Ver inventario →</small></a>
<a class="metrica-dashboard" href="pedidos.php"><span>Pedidos</span><strong><?php echo $total_pedidos;?></strong><small><?php echo $pendientes;?> pendientes →</small></a>
<a class="metrica-dashboard" href="pedidos.php"><span>Ventas registradas</span><strong><?php echo e(precio_texto($ventas));?></strong><small>Consultar pedidos →</small></a>
<a class="metrica-dashboard" href="clientes.php"><span>Clientes</span><strong><?php echo $total_clientes;?></strong><small>Ver clientes →</small></a>
</div></section>
</main>
<?php $db->close();require __DIR__.'/includes/pie.php';?>
