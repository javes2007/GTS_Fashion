<?php
require_once __DIR__.'/conectar.php'; exigir_cliente();
$db=conexion();$id=(int)$_SESSION['cliente_id'];$error='';$mensaje='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    $usuario=trim($_POST['usuario']??'');$nombre=trim($_POST['nombre']??'');$apellidos=trim($_POST['apellidos']??'');
    $correo=trim($_POST['correo']??'');$telefono=trim($_POST['telefono']??'');
    $nueva=$_POST['nueva_clave']??'';$confirmar=$_POST['confirmar_clave']??'';
    if(!preg_match('/^[a-zA-Z0-9_.-]{3,30}$/',$usuario))$error='El usuario debe tener entre 3 y 30 caracteres válidos.';
    elseif($correo!==''&&!filter_var($correo,FILTER_VALIDATE_EMAIL))$error='El correo electrónico no es válido.';
    elseif($nueva!==''&&strlen($nueva)<8)$error='La nueva contraseña debe tener al menos 8 caracteres.';
    elseif($nueva!==''&&$nueva!==$confirmar)$error='La confirmación de contraseña no coincide.';
    else{
        if($nueva!==''){$hash=password_hash($nueva,PASSWORD_DEFAULT);$stmt=$db->prepare('UPDATE clientes SET usuario=?,nombre=?,apellidos=?,correo=?,telefono=?,contrasena=? WHERE id=?');$stmt->bind_param('ssssssi',$usuario,$nombre,$apellidos,$correo,$telefono,$hash,$id);}
        else{$stmt=$db->prepare('UPDATE clientes SET usuario=?,nombre=?,apellidos=?,correo=?,telefono=? WHERE id=?');$stmt->bind_param('sssssi',$usuario,$nombre,$apellidos,$correo,$telefono,$id);}
        if($stmt->execute()){$_SESSION['cliente_nombre']=$nombre?:$usuario;$mensaje='Tu perfil fue actualizado correctamente.';}else{$error=$db->errno===1062?'Ese nombre de usuario o correo ya está registrado.':'No se pudo actualizar el perfil.';}
        $stmt->close();
    }
}
$stmt=$db->prepare('SELECT usuario,nombre,apellidos,correo,telefono FROM clientes WHERE id=?');$stmt->bind_param('i',$id);$stmt->execute();$cliente=$stmt->get_result()->fetch_assoc();$stmt->close();
$titulo='Mi perfil';require __DIR__.'/includes/encabezado.php';
?>
<main class="login-box registro-box"><h1>Mi perfil</h1><p>Edita tus datos cuando lo necesites.</p>
<?php if($mensaje):?><p class="aviso"><?php echo e($mensaje);?></p><?php endif;?><?php if($error):?><p class="error"><?php echo e($error);?></p><?php endif;?>
<form method="post" class="formulario-acceso">
<label>Nombre de usuario<input name="usuario" required minlength="3" maxlength="30" value="<?php echo e($_POST['usuario']??$cliente['usuario']);?>"></label>
<label>Nombre<input name="nombre" maxlength="100" value="<?php echo e($_POST['nombre']??$cliente['nombre']);?>"></label>
<label>Apellidos<input name="apellidos" maxlength="100" value="<?php echo e($_POST['apellidos']??$cliente['apellidos']);?>"></label>
<label>Correo electrónico<input type="email" name="correo" maxlength="120" value="<?php echo e($_POST['correo']??$cliente['correo']);?>"></label>
<label>Teléfono<input name="telefono" maxlength="20" value="<?php echo e($_POST['telefono']??$cliente['telefono']);?>"></label>
<label>Nueva contraseña (déjala vacía para conservar la actual)<span class="campo-password"><input id="nueva_clave" type="password" name="nueva_clave" minlength="8" autocomplete="new-password"><button class="mostrar-clave" type="button" data-toggle-password="nueva_clave" aria-label="Mostrar contraseña" aria-pressed="false"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg></button></span></label>
<label>Confirmar nueva contraseña<span class="campo-password"><input id="confirmar_clave" type="password" name="confirmar_clave" minlength="8" autocomplete="new-password"><button class="mostrar-clave" type="button" data-toggle-password="confirmar_clave" aria-label="Mostrar contraseña" aria-pressed="false"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7S2 19 2 19 2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg></button></span></label>
<button class="boton" type="submit">Guardar cambios</button></form></main>
<?php $db->close();require __DIR__.'/includes/pie.php';?>
