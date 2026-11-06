<?php
require_once __DIR__.'/conectar.php'; exigir_cliente();
$db=conexion();$id=(int)$_SESSION['cliente_id'];$error='';$mensaje='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    if(isset($_POST['eliminar_foto'])){
        $fotoStmt=$db->prepare('SELECT foto_perfil FROM clientes WHERE id=?');$fotoStmt->bind_param('i',$id);$fotoStmt->execute();$fotoRow=$fotoStmt->get_result()->fetch_assoc();$fotoStmt->close();
        if(!empty($fotoRow['foto_perfil'])){$base=realpath(__DIR__.'/uploads/perfiles');$real=realpath(__DIR__.'/'.$fotoRow['foto_perfil']);if($base&&$real&&strpos($real,$base.DIRECTORY_SEPARATOR)===0&&is_file($real))@unlink($real);}
        $del=$db->prepare('UPDATE clientes SET foto_perfil=NULL WHERE id=?');$del->bind_param('i',$id);$del->execute();$del->close();$mensaje='Fotografía eliminada.';
    } elseif(isset($_FILES['foto_perfil'])&&$_FILES['foto_perfil']['error']!==UPLOAD_ERR_NO_FILE){
        $f=$_FILES['foto_perfil'];if($f['error']!==UPLOAD_ERR_OK)$error='No se pudo recibir la fotografía.';
        elseif($f['size']>4*1024*1024)$error='La fotografía no puede superar 4 MB.';
        else{$info=@getimagesize($f['tmp_name']);$mime=$info['mime']??'';$ext=array('image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp');
            if(!$info||!isset($ext[$mime]))$error='Usa una imagen JPG, PNG o WEBP.';
            else{$dir=__DIR__.'/uploads/perfiles/';if(!is_dir($dir))mkdir($dir,0755,true);$nuevo='uploads/perfiles/'.bin2hex(random_bytes(16)).'.'.$ext[$mime];
                if(!move_uploaded_file($f['tmp_name'],__DIR__.'/'.$nuevo))$error='No se pudo guardar la fotografía.';
                else{$old=$db->prepare('SELECT foto_perfil FROM clientes WHERE id=?');$old->bind_param('i',$id);$old->execute();$oldrow=$old->get_result()->fetch_assoc();$old->close();
                    $save=$db->prepare('UPDATE clientes SET foto_perfil=? WHERE id=?');$save->bind_param('si',$nuevo,$id);
                    if($save->execute()){$base=realpath(__DIR__.'/uploads/perfiles');$real=realpath(__DIR__.'/'.$oldrow['foto_perfil']);if($base&&$real&&strpos($real,$base.DIRECTORY_SEPARATOR)===0&&is_file($real))@unlink($real);$mensaje='Fotografía actualizada.';}else{$error='No se pudo actualizar la fotografía.';@unlink(__DIR__.'/'.$nuevo);}$save->close();
                }
            }
        }
    }
    $usuario=trim($_POST['usuario']??'');$nombre=trim($_POST['nombre']??'');$apellidos=trim($_POST['apellidos']??'');
    $correo=trim($_POST['correo']??'');if($correo==='')$correo=null;$telefono=trim($_POST['telefono']??'');
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
$stmt=$db->prepare('SELECT usuario,nombre,apellidos,correo,telefono,foto_perfil FROM clientes WHERE id=?');$stmt->bind_param('i',$id);$stmt->execute();$cliente=$stmt->get_result()->fetch_assoc();$stmt->close();
$titulo='Mi perfil';require __DIR__.'/includes/encabezado.php';
?>
<main class="login-box registro-box"><h1>Mi perfil</h1><p>Edita tus datos cuando lo necesites.</p>
<?php if($mensaje):?><p class="aviso"><?php echo e($mensaje);?></p><?php endif;?><?php if($error):?><p class="error"><?php echo e($error);?></p><?php endif;?>
<div class="perfil-foto-panel"><div class="perfil-foto-circulo"><?php if(!empty($cliente['foto_perfil'])):?><img src="<?php echo e($cliente['foto_perfil']);?>" alt="Fotografía de perfil"><?php else:?><span><?php echo e(mb_strtoupper(mb_substr($cliente['usuario'],0,1)));?></span><?php endif;?></div><div><strong>Fotografía de perfil</strong><p>Imagen JPG, PNG o WEBP. Máximo 4 MB.</p><form method="post" enctype="multipart/form-data" class="formulario-foto"><input type="file" name="foto_perfil" accept="image/jpeg,image/png,image/webp" required><button class="boton" type="submit">Subir fotografía</button></form><?php if(!empty($cliente['foto_perfil'])):?><form method="post"><button class="boton boton-chico boton-peligro" type="submit" name="eliminar_foto" value="1">Eliminar fotografía</button></form><?php endif;?></div></div><form method="post" class="formulario-acceso">
<label>Nombre de usuario<input name="usuario" required minlength="3" maxlength="30" value="<?php echo e($_POST['usuario']??$cliente['usuario']);?>"></label>
<label>Nombre<input name="nombre" maxlength="100" value="<?php echo e($_POST['nombre']??$cliente['nombre']);?>"></label>
<label>Apellidos<input name="apellidos" maxlength="100" value="<?php echo e($_POST['apellidos']??$cliente['apellidos']);?>"></label>
<label>Correo electrónico<input type="email" name="correo" maxlength="120" value="<?php echo e($_POST['correo']??$cliente['correo']);?>"></label>
<label>Teléfono<input name="telefono" maxlength="20" value="<?php echo e($_POST['telefono']??$cliente['telefono']);?>"></label>
<label>Nueva contraseña (déjala vacía para conservar la actual)<span class="campo-password"><input id="nueva_clave" type="password" name="nueva_clave" minlength="8" autocomplete="new-password"><button class="mostrar-clave" type="button" data-toggle-password="nueva_clave" aria-label="Mostrar contraseña" aria-pressed="false"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg></button></span></label>
<label>Confirmar nueva contraseña<span class="campo-password"><input id="confirmar_clave" type="password" name="confirmar_clave" minlength="8" autocomplete="new-password"><button class="mostrar-clave" type="button" data-toggle-password="confirmar_clave" aria-label="Mostrar contraseña" aria-pressed="false"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7S2 19 2 19 2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg></button></span></label>
<button class="boton" type="submit">Guardar cambios</button></form></main>
<?php $db->close();require __DIR__.'/includes/pie.php';?>
