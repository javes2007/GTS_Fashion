<?php
require_once __DIR__.'/verificar_sesion.php';$titulo='Recursos del sitio';require __DIR__.'/includes/encabezado.php';
$carpeta=__DIR__.'/uploads';$archivos=array();if(is_dir($carpeta)){$lista=scandir($carpeta);foreach($lista as $f){if($f==='.'||$f==='..')continue;if(is_file($carpeta.'/'.$f))$archivos[]=$f;}}
?>
<main class="contenido modulo-admin"><span class="sobrelinea">SITIO WEB</span><h1>Recursos</h1><p>Biblioteca de imágenes y archivos que ya están almacenados en el proyecto.</p>
<section class="recursos-bloque"><div><h2>Biblioteca de imágenes</h2><span><?php echo count($archivos);?> archivos en la carpeta de recursos</span></div><div class="recursos-grid"><?php foreach(array_slice($archivos,0,24) as $archivo):?><figure><img src="<?php echo 'uploads/'.rawurlencode($archivo);?>" alt="<?php echo e($archivo);?>"><figcaption><?php echo e($archivo);?></figcaption></figure><?php endforeach;?><?php if(!$archivos):?><p>No hay imágenes cargadas en la carpeta uploads.</p><?php endif;?></div></section>
<section class="recursos-bloque" id="seo"><h2>SEO del sitio</h2><p>Revisa el título de la página, las descripciones, los textos alternativos de las imágenes y los enlaces antes de publicar.</p><span class="estado-informativo">Revisión pendiente</span></section>
<section class="recursos-bloque" id="dominio"><h2>Dominio</h2><p>El dominio se configura desde el proveedor donde está alojada la tienda (por ejemplo, el hosting o el servicio DNS).</p><span class="estado-informativo">Depende del alojamiento</span></section>
<section class="recursos-bloque" id="publicar"><h2>Publicar sitio</h2><p>Cuando termines de revisar el catálogo, los enlaces, el SEO y el dominio, despliega el proyecto desde tu hosting.</p><a class="boton boton-secundario" href="index.php">Vista previa de la tienda ↗</a></section>
</main>
<?php require __DIR__.'/includes/pie.php';?>
