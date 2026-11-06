<?php
require_once __DIR__.'/verificar_sesion.php';$titulo='Estilos de páginas';require __DIR__.'/includes/encabezado.php';
?>
<main class="contenido modulo-admin"><span class="sobrelinea">SITIO WEB</span><h1>Estilos de páginas</h1><p>Identidad visual aplicada actualmente a GTS Fashion. Esta vista documenta los estilos sin cambiar colores ni imágenes.</p>
<div class="vista-estilos"><section><h2>Paleta de marca</h2><div class="paleta-admin"><?php foreach(array('#fff0f8','#f9dcec','#e8b6e8','#d98abf','#8b286b','#5e1c49') as $color):?><div><i style="background:<?php echo $color;?>"></i><small><?php echo $color;?></small></div><?php endforeach;?></div></section>
<section><h2>Tipografía</h2><div class="muestra-tipografia"><strong>GTS Fashion</strong><span>Playfair Display · Poppins</span><p>Tu estilo, tu esencia.</p></div></section>
<section><h2>Componentes</h2><div class="muestra-componentes"><span class="etiqueta-demo">Colección</span><span class="boton-demo">Botón principal</span><span class="borde-demo">Tarjeta de contenido</span></div></section></div>
<p class="nota-admin">Los estilos originales se mantienen intactos. Un editor visual de colores y tipografías se puede conectar en una siguiente etapa.</p></main>
<?php require __DIR__.'/includes/pie.php';?>
