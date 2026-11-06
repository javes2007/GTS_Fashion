<?php
require_once __DIR__.'/conectar.php';
if (!empty($_SESSION['administrador_id'])) { header('Location: mostrar_datos.php'); exit; }
if (!empty($_SESSION['cliente_id'])) { header('Location: index.php'); exit; }
$error=''; $destino=$_GET['next']??$_POST['next']??'';
if(!in_array($destino,array('checkout'),true))$destino='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    $identificador=trim($_POST['identificador']??'');$clave=$_POST['clave']??'';
    $db=conexion();$stmt=$db->prepare('SELECT id,usuario,contrasena FROM administradores WHERE usuario=? LIMIT 1');
    $stmt->bind_param('s',$identificador);$stmt->execute();$admin=$stmt->get_result()->fetch_assoc();$stmt->close();
    if($admin&&password_verify($clave,$admin['contrasena'])){session_regenerate_id(true);$_SESSION['administrador_id']=(int)$admin['id'];$_SESSION['administrador_usuario']=$admin['usuario'];$db->close();header('Location: mostrar_datos.php');exit;}
    // Compatibilidad: los clientes antiguos también pueden entrar temporalmente con su correo.
    $stmt=$db->prepare('SELECT id,usuario,nombre,contrasena FROM clientes WHERE usuario=? OR correo=? LIMIT 1');
    $stmt->bind_param('ss',$identificador,$identificador);$stmt->execute();$cliente=$stmt->get_result()->fetch_assoc();$stmt->close();$db->close();
    if($cliente&&password_verify($clave,$cliente['contrasena'])){session_regenerate_id(true);$_SESSION['cliente_id']=(int)$cliente['id'];$_SESSION['cliente_nombre']=$cliente['nombre']?:$cliente['usuario'];header('Location: '.($destino==='checkout'?'checkout.php':'index.php'));exit;}
    $error='El usuario o la contraseña no son correctos.';
}
$titulo='Iniciar sesión';require __DIR__.'/includes/encabezado.php';
?>
<main class="login-box"><h1>Iniciar sesión</h1><p>Ingresa con tu nombre de usuario y contraseña.</p>
<?php if($error):?><p class="error"><?php echo e($error);?></p><?php endif;?>
<form method="post" class="formulario-acceso"><input type="hidden" name="next" value="<?php echo e($destino);?>">
<label>Nombre de usuario<input name="identificador" required autocomplete="username" value="<?php echo e($_POST['identificador']??'');?>"></label>
<label>Contraseña<span class="campo-password"><input id="clave" type="password" name="clave" required autocomplete="current-password"><button class="mostrar-clave" type="button" data-toggle-password="clave" aria-label="Mostrar contraseña" aria-pressed="false"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg></button></span></label>
<button class="boton" type="submit">Ingresar</button></form>
<p>¿Todavía no tienes cuenta? <a href="registrar.php">Crear cuenta</a></p></main>
<?php require __DIR__.'/includes/pie.php';?>
