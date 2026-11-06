<?php
require_once __DIR__ . '/conectar.php';
if (!empty($_SESSION['administrador_id'])) { header('Location: mostrar_datos.php'); exit; }
if (!empty($_SESSION['cliente_id'])) { header('Location: index.php'); exit; }
$error = '';
$destino = $_GET['next'] ?? $_POST['next'] ?? '';
if (!in_array($destino, array('checkout'), true)) $destino = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identificador = trim($_POST['identificador'] ?? '');
    $clave = $_POST['clave'] ?? '';
    $db = conexion();
    $stmt = $db->prepare('SELECT id, usuario, contrasena FROM administradores WHERE usuario = ? LIMIT 1');
    $stmt->bind_param('s', $identificador); $stmt->execute();
    $admin = $stmt->get_result()->fetch_assoc(); $stmt->close();
    if ($admin && password_verify($clave, $admin['contrasena'])) {
        session_regenerate_id(true);
        $_SESSION['administrador_id'] = (int)$admin['id'];
        $_SESSION['administrador_usuario'] = $admin['usuario'];
        $db->close(); header('Location: mostrar_datos.php'); exit;
    }
    $stmt = $db->prepare('SELECT id,nombre,apellidos,correo,contrasena FROM clientes WHERE correo=? LIMIT 1');
    $stmt->bind_param('s', $identificador); $stmt->execute();
    $cliente = $stmt->get_result()->fetch_assoc(); $stmt->close(); $db->close();
    if ($cliente && password_verify($clave, $cliente['contrasena'])) {
        session_regenerate_id(true);
        $_SESSION['cliente_id'] = (int)$cliente['id'];
        $_SESSION['cliente_nombre'] = $cliente['nombre'];
        header('Location: ' . ($destino === 'checkout' ? 'checkout.php' : 'index.php')); exit;
    }
    $error = 'Los datos de acceso no son correctos.';
}
$titulo = 'Iniciar sesión'; require __DIR__ . '/includes/encabezado.php';
?>
<main class="login-box">
    <h1>Iniciar sesión</h1>
    <p>Ingresa con tu cuenta para continuar.</p>
    <?php if ($error): ?><p class="error"><?php echo e($error); ?></p><?php endif; ?>
    <form method="post" class="formulario-acceso">
        <input type="hidden" name="next" value="<?php echo e($destino); ?>">
        <label>Usuario o correo electrónico<input name="identificador" required autocomplete="username" value="<?php echo e($_POST['identificador'] ?? ''); ?>"></label>
        <label>Contraseña<input type="password" name="clave" required autocomplete="current-password"></label>
        <button class="boton" type="submit">Ingresar</button>
    </form>
    <p>¿Todavía no tienes cuenta? <a href="registrar.php">Crear cuenta</a></p>
</main>
<?php require __DIR__ . '/includes/pie.php'; ?>
