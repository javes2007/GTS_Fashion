<?php
require_once __DIR__.'/conectar.php';
if (!empty($_SESSION['cliente_id'])) { header('Location: index.php'); exit; }
$error='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $usuario=trim($_POST['usuario']??''); $clave=$_POST['clave']??''; $confirmar=$_POST['confirmar']??'';
    if (!preg_match('/^[a-zA-Z0-9_.-]{3,30}$/',$usuario)) $error='El usuario debe tener entre 3 y 30 caracteres: letras, números, punto, guion o guion bajo.';
    elseif (strlen($clave)<8) $error='La contraseña debe tener al menos 8 caracteres.';
    elseif ($clave!==$confirmar) $error='Las contraseñas no coinciden.';
    else {
        $db=conexion(); $hash=password_hash($clave,PASSWORD_DEFAULT);
        $stmt=$db->prepare('INSERT INTO clientes(usuario,contrasena) VALUES(?,?)');
        $stmt->bind_param('ss',$usuario,$hash);
        if ($stmt->execute()) {
            $id=$stmt->insert_id; session_regenerate_id(true);
            $_SESSION['cliente_id']=(int)$id; $_SESSION['cliente_nombre']=$usuario;
            $stmt->close(); $db->close(); header('Location: index.php'); exit;
        }
        $error=$db->errno===1062?'Ese nombre de usuario ya está en uso. Elige otro.':'No fue posible crear la cuenta. Comprueba que aplicaste la migración de cuentas.';
        $stmt->close(); $db->close();
    }
}
$titulo='Crear cuenta'; require __DIR__.'/includes/encabezado.php';
?>
<main class="login-box registro-box">
<h1>Crear cuenta</h1><p>Regístrate con un nombre de usuario y una contraseña.</p>
<?php if($error):?><p class="error"><?php echo e($error);?></p><?php endif;?>
<form method="post" class="formulario-acceso"><input type="hidden" name="csrf" value="<?php echo e(csrf_token());?>">
<label>Nombre de usuario<input name="usuario" minlength="3" maxlength="30" pattern="[A-Za-z0-9_.-]{3,30}" required autocomplete="username" value="<?php echo e($_POST['usuario']??'');?>"></label>
<label>Contraseña<span class="campo-password"><input id="clave" type="password" name="clave" minlength="8" required autocomplete="new-password"><button class="mostrar-clave" type="button" data-toggle-password="clave" aria-label="Mostrar contraseña" aria-pressed="false"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg></button></span></label>
<label>Confirmar contraseña<span class="campo-password"><input id="confirmar" type="password" name="confirmar" minlength="8" required autocomplete="new-password"><button class="mostrar-clave" type="button" data-toggle-password="confirmar" aria-label="Mostrar contraseña" aria-pressed="false"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg></button></span></label>
<button class="boton" type="submit">Crear mi cuenta</button></form>
<p>¿Ya tienes cuenta? <a href="login.php">Iniciar sesión</a></p></main>
<?php require __DIR__.'/includes/pie.php'; ?>
