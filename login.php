<?php
require_once __DIR__ . '/conectar.php';
if (!empty($_SESSION['administrador_id'])) {
    header('Location: mostrar_datos.php');
    exit;
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $clave = $_POST['clave'] ?? '';
    $db = conexion();
    $stmt = $db->prepare('SELECT id, usuario, contrasena FROM administradores WHERE usuario = ? LIMIT 1');
    $stmt->bind_param('s', $usuario);
    $stmt->execute();
    $admin = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $db->close();
    if ($admin && password_verify($clave, $admin['contrasena'])) {
        session_regenerate_id(true);
        $_SESSION['administrador_id'] = (int)$admin['id'];
        $_SESSION['administrador_usuario'] = $admin['usuario'];
        header('Location: mostrar_datos.php');
        exit;
    }
    $error = 'Usuario o contraseña incorrectos.';
}
$titulo = 'Iniciar sesión';
require __DIR__ . '/includes/encabezado.php';
?>
<main class="login-box">
    <h1>Administración</h1>
    <?php if ($error): ?><p class="error"><?php echo e($error); ?></p><?php endif; ?>
    <form method="post">
        <label>Usuario<input name="usuario" required autocomplete="username"></label>
        <label>Contraseña<input type="password" name="clave" required autocomplete="current-password"></label>
        <button class="boton" type="submit">Ingresar</button>
    </form>
</main>
<?php require __DIR__ . '/includes/pie.php'; ?>
