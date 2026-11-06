<?php
require_once __DIR__.'/conectar.php';
if (!empty($_SESSION['cliente_id'])) { header('Location: index.php'); exit; }
$error='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $nombre=trim($_POST['nombre']??''); $apellidos=trim($_POST['apellidos']??'');
    $documento=trim($_POST['documento']??''); $correo=strtolower(trim($_POST['correo']??''));
    $telefono=trim($_POST['telefono']??''); $direccion=trim($_POST['direccion']??'');
    $clave=$_POST['clave']??''; $confirmar=$_POST['confirmar']??'';
    if ($nombre===''||$apellidos===''||$documento===''||!filter_var($correo,FILTER_VALIDATE_EMAIL)||$telefono===''||$direccion==='') $error='Completa todos los campos con información válida.';
    elseif (strlen($clave)<8) $error='La contraseña debe tener al menos 8 caracteres.';
    elseif ($clave!==$confirmar) $error='Las contraseñas no coinciden.';
    else {
        $db=conexion(); $hash=password_hash($clave,PASSWORD_DEFAULT);
        $stmt=$db->prepare('INSERT INTO clientes(nombre,apellidos,documento,correo,telefono,direccion,contrasena) VALUES(?,?,?,?,?,?,?)');
        $stmt->bind_param('sssssss',$nombre,$apellidos,$documento,$correo,$telefono,$direccion,$hash);
        if ($stmt->execute()) {
            $id=$stmt->insert_id; session_regenerate_id(true);
            $_SESSION['cliente_id']=(int)$id; $_SESSION['cliente_nombre']=$nombre;
            $stmt->close(); $db->close(); header('Location: index.php'); exit;
        }
        $error=$db->errno===1062?'Ya existe una cuenta con ese documento o correo.':'No fue posible crear la cuenta. Revisa los datos.';
        $stmt->close(); $db->close();
    }
}
$titulo='Crear cuenta'; require __DIR__.'/includes/encabezado.php';
?>
<main class="login-box registro-box"><h1>Crear cuenta</h1><p>Regístrate para guardar tus compras y consultar tus pedidos.</p>
<?php if($error):?><p class="error"><?php echo e($error);?></p><?php endif;?>
<form method="post" class="formulario-acceso">
<label>Nombre<input name="nombre" maxlength="100" required value="<?php echo e($_POST['nombre']??'');?>"></label>
<label>Apellidos<input name="apellidos" maxlength="100" required value="<?php echo e($_POST['apellidos']??'');?>"></label>
<label>Documento<input name="documento" maxlength="30" required value="<?php echo e($_POST['documento']??'');?>"></label>
<label>Correo electrónico<input type="email" name="correo" maxlength="120" required value="<?php echo e($_POST['correo']??'');?>"></label>
<label>Teléfono<input name="telefono" maxlength="20" required value="<?php echo e($_POST['telefono']??'');?>"></label>
<label>Dirección<input name="direccion" maxlength="255" required value="<?php echo e($_POST['direccion']??'');?>"></label>
<label>Contraseña<input type="password" name="clave" minlength="8" required autocomplete="new-password"></label>
<label>Confirmar contraseña<input type="password" name="confirmar" minlength="8" required autocomplete="new-password"></label>
<button class="boton" type="submit">Crear mi cuenta</button></form><p>¿Ya tienes cuenta? <a href="login.php">Iniciar sesión</a></p></main>
<?php require __DIR__.'/includes/pie.php'; ?>
