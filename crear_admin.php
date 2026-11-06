<?php
require_once __DIR__ . '/conectar.php';
$config = file_exists(__DIR__ . '/config.local.php') ? require __DIR__ . '/config.local.php' : array();
$clave_configurada = isset($config['setup_key']) ? $config['setup_key'] : '';
$error = '';
$mensaje = '';
if ($clave_configurada === '' || $clave_configurada === 'CAMBIA-ESTA-CLAVE-DE-INSTALACION') {
    $error = 'Configura primero una clave privada setup_key en config.local.php.';
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $llave = $_POST['llave'] ?? '';
    $usuario = trim($_POST['usuario'] ?? '');
    $clave = $_POST['clave'] ?? '';
    if (!hash_equals($clave_configurada, $llave)) {
        $error = 'Clave de instalación incorrecta.';
    } elseif ($usuario === '' || strlen($clave) < 8) {
        $error = 'Escribe un usuario y una contraseña de al menos 8 caracteres.';
    } else {
        $hash = password_hash($clave, PASSWORD_DEFAULT);
        $db = conexion();
        $stmt = $db->prepare('INSERT INTO administradores (usuario, contrasena) VALUES (?, ?) ON DUPLICATE KEY UPDATE contrasena = VALUES(contrasena)');
        $stmt->bind_param('ss', $usuario, $hash);
        if ($stmt->execute()) $mensaje = 'Administrador creado o actualizado. Ya puedes iniciar sesión.';
        else $error = 'No se pudo crear el administrador: ' . $stmt->error;
        $stmt->close();
        $db->close();
    }
}
$titulo = 'Crear administrador';
require __DIR__ . '/includes/encabezado.php';
?>
<main class="login-box">
    <h1>Crear administrador</h1>
    <p>Después de crear la cuenta, elimina este archivo del servidor o cambia setup_key.</p>
    <?php if ($error): ?><p class="error"><?php echo e($error); ?></p><?php endif; ?>
    <?php if ($mensaje): ?><p class="aviso"><?php echo e($mensaje); ?></p><?php endif; ?>
    <form method="post">
        <label>Clave de instalación<input type="password" name="llave" required></label>
        <label>Usuario administrador<input name="usuario" required></label>
        <label>Contraseña (mínimo 8 caracteres)<input type="password" name="clave" minlength="8" required></label>
        <button class="boton">Crear administrador</button>
    </form>
</main>
<?php require __DIR__ . '/includes/pie.php'; ?>
