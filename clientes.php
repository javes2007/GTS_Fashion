<?php
require_once __DIR__.'/verificar_sesion.php';$db=conexion();$q=trim($_GET['q']??'');
if($q!==''){$like='%'.$q.'%';$stmt=$db->prepare('SELECT id,usuario,nombre,apellidos,correo,telefono,fecha_registro FROM clientes WHERE usuario LIKE ? OR nombre LIKE ? OR apellidos LIKE ? OR correo LIKE ? ORDER BY id DESC');$stmt->bind_param('ssss',$like,$like,$like,$like);}else{$stmt=$db->prepare('SELECT id,usuario,nombre,apellidos,correo,telefono,fecha_registro FROM clientes ORDER BY id DESC');}
$tiene_usuario=true;
try{$stmt->execute();$res=$stmt->get_result();}
catch(mysqli_sql_exception $ex){$tiene_usuario=false;if($q!==''){$like='%'.$q.'%';$stmt=$db->prepare('SELECT id,nombre,apellidos,correo,telefono,fecha_registro FROM clientes WHERE nombre LIKE ? OR apellidos LIKE ? OR correo LIKE ? ORDER BY id DESC');$stmt->bind_param('sss',$like,$like,$like);}else{$stmt=$db->prepare('SELECT id,nombre,apellidos,correo,telefono,fecha_registro FROM clientes ORDER BY id DESC');}$stmt->execute();$res=$stmt->get_result();}
$titulo='Clientes';require __DIR__.'/includes/encabezado.php';
?>
<main class="contenido modulo-admin"><span class="sobrelinea">PRODUCTOS Y SERVICIOS · CLIENTES</span><h1>Clientes</h1><?php if(isset($_GET['msg'])):?><p class="aviso"><?php echo e($_GET['msg']);?></p><?php endif;?><p>Consulta las cuentas registradas y sus datos de contacto.</p>
<form class="buscador" method="get"><input type="search" name="q" placeholder="Buscar por usuario, nombre o correo" value="<?php echo e($q);?>"><button class="boton">Buscar</button></form>
<div class="tabla-scroll" style="margin-top:18px"><table><thead><tr><th>Usuario</th><th>Nombre</th><th>Correo</th><th>Teléfono</th><th>Registro</th><th>Acciones</th></tr></thead><tbody>
<?php if(!$res->num_rows):?><tr><td colspan="6">No se encontraron clientes.</td></tr><?php endif;?>
<?php while($c=$res->fetch_assoc()):?><tr><td><?php echo e($tiene_usuario?$c['usuario']:'—');?></td><td><?php echo e(trim(($c['nombre']??'').' '.($c['apellidos']??''))?:'Sin completar');?></td><td><?php echo e($c['correo']?:'—');?></td><td><?php echo e($c['telefono']?:'—');?></td><td><?php echo e($c['fecha_registro']);?></td><td class="acciones"><a class="boton" href="cliente_editar.php?id=<?php echo (int)$c['id'];?>">Editar</a><form method="post" action="cliente_eliminar.php" onsubmit="return confirm('¿Eliminar esta cuenta? Solo es posible si no tiene pedidos.');"><input type="hidden" name="csrf" value="<?php echo e(csrf_token());?>"><input type="hidden" name="id" value="<?php echo (int)$c['id'];?>"><button class="boton boton-peligro">Eliminar</button></form></td></tr><?php endwhile;?>
</tbody></table></div></main>
<?php $stmt->close();$db->close();require __DIR__.'/includes/pie.php';?>
