<?php
require_once __DIR__.'/verificar_sesion.php';
$db=conexion();$error='';$mensaje='';$editar=null;
if($_SERVER['REQUEST_METHOD']==='POST'){
    if(!csrf_validar($_POST['csrf']??''))$error='La sesión del formulario venció. Recarga la página e inténtalo de nuevo.';
    else{
        $accion=$_POST['accion']??'';$id=(int)($_POST['id']??0);$nombre=trim($_POST['nombre']??'');
        if($accion==='guardar'){
            if($nombre===''||mb_strlen($nombre)>80)$error='Escribe un nombre de categoría de máximo 80 caracteres.';
            else if($id>0){$s=$db->prepare('UPDATE categorias SET nombre=? WHERE id=?');$s->bind_param('si',$nombre,$id);if($s->execute())$mensaje='Categoría actualizada.';else$error=$db->errno===1062?'Ya existe una categoría con ese nombre.':'No se pudo actualizar la categoría.';$s->close();}
            else{$s=$db->prepare('INSERT INTO categorias(nombre) VALUES(?)');$s->bind_param('s',$nombre);if($s->execute())$mensaje='Categoría creada.';else$error=$db->errno===1062?'Ya existe una categoría con ese nombre.':'No se pudo crear la categoría.';$s->close();}
        }elseif($accion==='eliminar'&&$id>0){
            $s=$db->prepare('SELECT COUNT(*) n FROM productos WHERE categoria_id=?');$s->bind_param('i',$id);$s->execute();$n=(int)$s->get_result()->fetch_assoc()['n'];$s->close();
            if($n>0)$error='No puedes eliminar esta categoría porque tiene productos asociados. Reasigna esos productos primero.';
            else{$s=$db->prepare('DELETE FROM categorias WHERE id=?');$s->bind_param('i',$id);if($s->execute()&&$s->affected_rows)$mensaje='Categoría eliminada.';else$error='No se encontró la categoría.';$s->close();}
        }
    }
}
if(isset($_GET['editar'])){$id=(int)$_GET['editar'];$s=$db->prepare('SELECT id,nombre FROM categorias WHERE id=?');$s->bind_param('i',$id);$s->execute();$editar=$s->get_result()->fetch_assoc();$s->close();}
$res=$db->query('SELECT c.id,c.nombre,COUNT(p.id) productos FROM categorias c LEFT JOIN productos p ON p.categoria_id=c.id GROUP BY c.id,c.nombre ORDER BY c.nombre');
$titulo='Categorías';require __DIR__.'/includes/encabezado.php';
?>
<main class="contenido modulo-admin"><span class="sobrelinea">CATÁLOGO</span><h1>Categorías</h1><p>Administra las categorías que organizan los productos de la tienda.</p>
<?php if($mensaje):?><p class="aviso"><?php echo e($mensaje);?></p><?php endif;?><?php if($error):?><p class="error"><?php echo e($error);?></p><?php endif;?>
<section class="login-box"><h2><?php echo $editar?'Editar categoría':'Nueva categoría';?></h2><form method="post" class="formulario-acceso"><input type="hidden" name="csrf" value="<?php echo e(csrf_token());?>"><input type="hidden" name="accion" value="guardar"><input type="hidden" name="id" value="<?php echo (int)($editar['id']??0);?>"><label>Nombre<input name="nombre" maxlength="80" required value="<?php echo e($editar['nombre']??'');?>"></label><button class="boton">Guardar categoría</button><?php if($editar):?><a href="categorias.php">Cancelar edición</a><?php endif;?></form></section>
<div class="tabla-scroll" style="margin-top:24px"><table><thead><tr><th>Categoría</th><th>Productos asociados</th><th>Acciones</th></tr></thead><tbody><?php while($c=$res->fetch_assoc()):?><tr><td><?php echo e($c['nombre']);?></td><td><?php echo (int)$c['productos'];?></td><td class="acciones"><a class="boton" href="categorias.php?editar=<?php echo (int)$c['id'];?>">Editar</a><form method="post" onsubmit="return confirm('¿Eliminar esta categoría? Solo se permite si no tiene productos asociados.');"><input type="hidden" name="csrf" value="<?php echo e(csrf_token());?>"><input type="hidden" name="accion" value="eliminar"><input type="hidden" name="id" value="<?php echo (int)$c['id'];?>"><button class="boton boton-peligro">Eliminar</button></form></td></tr><?php endwhile;?></tbody></table></div></main>
<?php $db->close();require __DIR__.'/includes/pie.php';?>
