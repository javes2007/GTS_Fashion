<?php
require_once __DIR__.'/verificar_sesion.php';$titulo='Páginas del sitio';require __DIR__.'/includes/encabezado.php';
?>
<main class="contenido modulo-admin"><div class="dashboard-topline"><span class="sobrelinea">SITIO WEB</span><a class="boton boton-secundario" href="index.php">Ver sitio ↗</a></div><h1>Páginas</h1><p>Estas son las secciones que componen la experiencia pública de GTS Fashion.</p>
<div class="lista-paginas-admin">
<article><div class="mini-pagina">GTS</div><div><strong>Inicio / Tienda</strong><small>Página principal y presentación del catálogo.</small></div><a href="index.php">Abrir ↗</a></article>
<article><div class="mini-pagina">CAT</div><div><strong>Catálogo</strong><small>Productos organizados por categorías.</small></div><a href="index.php#catalogo">Abrir ↗</a></article>
<article><div class="mini-pagina">INF</div><div><strong>Acerca de</strong><small>Información de la marca.</small></div><a href="index.php#acerca">Abrir ↗</a></article>
<article><div class="mini-pagina">CON</div><div><strong>Contacto</strong><small>Sección de contacto de la tienda.</small></div><a href="index.php#contacto">Abrir ↗</a></article>
</div><p class="nota-admin">Las páginas existentes se muestran aquí como estructura del sitio. La edición de sus textos y bloques requiere conectar un editor de contenido.</p></main>
<?php require __DIR__.'/includes/pie.php';?>
