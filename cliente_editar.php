<?php
require_once __DIR__.'/verificar_sesion.php';$db=conexion();$id=(int)($_GET['id']??$_POST['id']??0);$error='';$mensaje='';
$s=$db->prepare('SELECT id,usuario,nombre,apellidos,correo,telefono,direccion FROM clientes WHERE id=?');$s->bind_param('i',$id);$s->execute();$cliente=$s->get_result()->fetch_assoc();$s->close();
if(!$cliente){$db->close();header('Location: clientes.php?msg='.urlencode('Cliente no encontrado.'));exit;}
if($_SERVER['REQUEST_METHOD']==='POST'){
 if(!csrf_validar($_POST['csrf']??''))$error='Formulario vencido. Recarga e inténtalo de nuevo.';
 else{
  $usuario=trim($_POST['usuario']??'');$nombre=trim($_POST['nombre']??'');$apellidos=trim($_POST['apellidos']??'');$correo=trim($_POST['correo']??'');if($correo==='')$correo=null;$telefono=trim($_POST['telefono']??'');$direccion=trim($_POST['direccion']??'');
  if(!preg_match('/^[a-zA-Z0-9_.-]{3,30}$/',$usuario))$error='Usuario no válido.';
  elseif($correo!==null&&!filter_var($correo,FILTER_VALIDATE_EMAIL))$error='Correo no válido.';
  else{$s=$db->prepare('UPDATE clientes SET usuario=?,nombre=?,apellidos=?,correo=?,telefono=?,direccion=? WHERE id=?');$s->bind_param('ssssssi',$usuario,$nombre,$apellidos,$correo,$telefono,$direccion,$id);if($s->execute()){$mensaje='Cliente actualizado.';$cliente=array_merge($cliente,array('usuario'=>$usuario,'nombre'=>$nombre,'apellidos'=>$apellidos,'correo'=>$correo,'telefono'=>$telefono,'direccion'=>$direccion));}else$error=$db->errno===1062?'El usuario o correo ya pertenece a otra cuenta.':'No se pudo actualizar el cliente.';$s->close();}
 }
}
$titulo='Editar cliente';require __DIR__.'/includes/encabezado.php';
?>
<main class="contenido modulo-admin"><h1>Editar cliente #<?php echo $id;?></h1><?php if($mensaje):?><p class="aviso"><?php echo e($mensaje);?></p><?php endif;?><?php if($error):?><p class="error"><?php echo e($error);?></p><?php endif;?>
<form method="post" class="formulario-acceso"><input type="hidden" name="csrf" value="<?php echo e(csrf_token());?>"><input type="hidden" name="id" value="<?php echo $id;?>">
<label>Usuario<input name="usuario" maxlength="30" required value="<?php echo e($cliente['usuario']);?>"></label><label>Nombre<input name="nombre" maxlength="100" value="<?php echo e($cliente['nombre']);?>"></label><label>Apellidos<input name="apellidos" maxlength="100" value="<?php echo e($cliente['apellidos']);?>"></label><label>Correo<input type="email" name="correo" maxlength="120" value="<?php echo e($cliente['correo']);?>"></label><label>Teléfono<input name="telefono" maxlength="20" value="<?php echo e($cliente['telefono']);?>"></label><label>Dirección<input name="direccion" maxlength="255" value="<?php echo e($cliente['direccion']);?>"></label><button class="boton">Guardar cambios</button><a href="clientes.php">Volver</a></form></main>
<?php $db->close();require __DIR__.'/includes/pie.php';?>
