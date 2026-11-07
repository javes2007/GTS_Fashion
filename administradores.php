<?php
require_once __DIR__.'/verificar_sesion.php';$db=conexion();$error='';$mensaje='';$actual=(int)$_SESSION['administrador_id'];
if($_SERVER['REQUEST_METHOD']==='POST'){
 if(!csrf_validar($_POST['csrf']??''))$error='Formulario vencido. Recarga e inténtalo de nuevo.';
 else{$accion=$_POST['accion']??'';$id=(int)($_POST['id']??0);$usuario=trim($_POST['usuario']??'');$correo=trim($_POST['correo']??'');if($correo==='')$correo=null;$clave=$_POST['clave']??'';
  if($accion==='guardar'){
   if(!preg_match('/^[a-zA-Z0-9_.-]{3,50}$/',$usuario))$error='El usuario debe tener entre 3 y 50 caracteres válidos.';
   elseif($correo!==null&&!filter_var($correo,FILTER_VALIDATE_EMAIL))$error='Correo electrónico no válido.';
   elseif($id===0&&strlen($clave)<8)$error='La contraseña inicial debe tener al menos 8 caracteres.';
   elseif($clave!==''&&strlen($clave)<8)$error='La contraseña debe tener al menos 8 caracteres.';
   else{
    if($id>0){if($clave!==''){$hash=password_hash($clave,PASSWORD_DEFAULT);$s=$db->prepare('UPDATE administradores SET usuario=?,correo=?,contrasena=? WHERE id=?');$s->bind_param('sssi',$usuario,$correo,$hash,$id);}else{$s=$db->prepare('UPDATE administradores SET usuario=?,correo=? WHERE id=?');$s->bind_param('ssi',$usuario,$correo,$id);}}
    else{$hash=password_hash($clave,PASSWORD_DEFAULT);$s=$db->prepare('INSERT INTO administradores(usuario,correo,contrasena) VALUES(?,?,?)');$s->bind_param('sss',$usuario,$correo,$hash);}
    if($s->execute())$mensaje='Administrador guardado.';else$error=$db->errno===1062?'El usuario o correo ya está registrado.':'No se pudo guardar el administrador. Comprueba la migración de correo.';$s->close();
   }
  }elseif($accion==='eliminar'&&$id>0){
   if($id===$actual)$error='No puedes eliminar la cuenta con la que tienes iniciada sesión.';
   else{$count=(int)$db->query('SELECT COUNT(*) n FROM administradores')->fetch_assoc()['n'];if($count<=1)$error='Debe existir al menos un administrador.';else{$s=$db->prepare('DELETE FROM administradores WHERE id=?');$s->bind_param('i',$id);if($s->execute()&&$s->affected_rows)$mensaje='Administrador eliminado.';else$error='No se encontró el administrador.';$s->close();}}
  }
 }
}
$editar=null;if(isset($_GET['editar'])){$id=(int)$_GET['editar'];$s=$db->prepare('SELECT id,usuario,correo FROM administradores WHERE id=?');$s->bind_param('i',$id);$s->execute();$editar=$s->get_result()->fetch_assoc();$s->close();}
$res=$db->query('SELECT id,usuario,correo,fecha_registro FROM administradores ORDER BY id');
$titulo='Administradores';require __DIR__.'/includes/encabezado.php';
?>
<main class="contenido modulo-admin"><span class="sobrelinea">SEGURIDAD Y ACCESOS</span><h1>Administradores</h1><p>Gestiona las cuentas con acceso al panel. Utiliza contraseñas únicas y seguras.</p><?php if($mensaje):?><p class="aviso"><?php echo e($mensaje);?></p><?php endif;?><?php if($error):?><p class="error"><?php echo e($error);?></p><?php endif;?>
<section class="login-box"><h2><?php echo $editar?'Editar administrador':'Crear administrador';?></h2><form method="post" class="formulario-acceso"><input type="hidden" name="csrf" value="<?php echo e(csrf_token());?>"><input type="hidden" name="accion" value="guardar"><input type="hidden" name="id" value="<?php echo (int)($editar['id']??0);?>"><label>Usuario<input name="usuario" maxlength="50" required value="<?php echo e($editar['usuario']??'');?>"></label><label>Correo<input type="email" name="correo" maxlength="120" value="<?php echo e($editar['correo']??'');?>"></label><label><?php echo $editar?'Nueva contraseña (opcional)':'Contraseña (mínimo 8 caracteres)';?><input type="password" name="clave" minlength="8" <?php echo $editar?'':'required';?> autocomplete="new-password"></label><button class="boton">Guardar</button><?php if($editar):?><a href="administradores.php">Cancelar</a><?php endif;?></form></section>
<div class="tabla-scroll" style="margin-top:24px"><table><thead><tr><th>Usuario</th><th>Correo</th><th>Creado</th><th>Acciones</th></tr></thead><tbody><?php while($a=$res->fetch_assoc()):?><tr><td><?php echo e($a['usuario']);?><?php if((int)$a['id']===$actual):?> (sesión actual)<?php endif;?></td><td><?php echo e($a['correo']?:'—');?></td><td><?php echo e($a['fecha_registro']);?></td><td class="acciones"><a class="boton" href="administradores.php?editar=<?php echo (int)$a['id'];?>">Editar</a><?php if((int)$a['id']!==$actual):?><form method="post" onsubmit="return confirm('¿Eliminar este administrador?');"><input type="hidden" name="csrf" value="<?php echo e(csrf_token());?>"><input type="hidden" name="accion" value="eliminar"><input type="hidden" name="id" value="<?php echo (int)$a['id'];?>"><button class="boton boton-peligro">Eliminar</button></form><?php endif;?></td></tr><?php endwhile;?></tbody></table></div></main>
<?php $db->close();require __DIR__.'/includes/pie.php';?>
