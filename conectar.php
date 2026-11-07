<?php
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.use_strict_mode', '1');
    // "Recordarme" conserva la sesión durante 30 días; sin marcar, usa sesión normal.
    ini_set('session.gc_maxlifetime', '2592000');
    $recordar = !empty($_POST['recordarme']);
    $duracion = $recordar ? 60 * 60 * 24 * 30 : 0;
    session_set_cookie_params(array(
        'lifetime' => $duracion,
        'path' => '/',
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Lax'
    ));
    session_start();
}
function conexion() {
    $config = array('host'=>'localhost','usuario'=>'root','clave'=>'','base'=>'gts_fashion');
    $archivo = __DIR__ . '/config.local.php';
    if (file_exists($archivo)) { $local = require $archivo; if (is_array($local)) $config = array_merge($config, $local); }
    $db = new mysqli($config['host'],$config['usuario'],$config['clave'],$config['base']);
    if ($db->connect_error) die('No se pudo conectar con MySQL. Revisa config.local.php.');
    $db->set_charset('utf8mb4'); return $db;
}
function e($valor) { return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8'); }
function precio_texto($precio) { return ($precio === null || $precio === '') ? 'Precio por definir' : '$' . number_format((float)$precio,0,',','.'); }
function imagen_prenda($ruta,$nombre) {
    if (!$ruta) return '<div class="sin-imagen">Sin imagen</div>';
    return '<img src="' . e($ruta) . '" alt="' . e($nombre) . '">';
}
function subir_imagen($campo,&$error) {
    if (!isset($_FILES[$campo]) || $_FILES[$campo]['error'] === UPLOAD_ERR_NO_FILE) return null;
    if ($_FILES[$campo]['error'] !== UPLOAD_ERR_OK) { $error='No se pudo recibir el archivo.'; return null; }
    if ($_FILES[$campo]['size'] > 4*1024*1024) { $error='La imagen no puede superar 4 MB.'; return null; }
    $info=getimagesize($_FILES[$campo]['tmp_name']); if ($info===false) { $error='El archivo no es una imagen válida.'; return null; }
    $tipos=array('image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'); $mime=$info['mime'];
    if (!isset($tipos[$mime])) { $error='Solo se permiten imágenes JPG, PNG o WEBP.'; return null; }
    $carpeta=__DIR__.'/uploads/'; if (!is_dir($carpeta)) mkdir($carpeta,0755,true);
    $nombre_archivo=bin2hex(random_bytes(12)).'.'.$tipos[$mime];
    if (!move_uploaded_file($_FILES[$campo]['tmp_name'],$carpeta.$nombre_archivo)) { $error='No se pudo guardar la imagen.'; return null; }
    return 'uploads/'.$nombre_archivo;
}
function borrar_imagen($ruta) {
    if (!$ruta) return; $base=realpath(__DIR__.'/uploads'); $archivo=realpath(__DIR__.'/'.$ruta);
    if ($base && $archivo && strpos($archivo,$base.DIRECTORY_SEPARATOR)===0 && is_file($archivo)) unlink($archivo);
}
function csrf_token() {
    if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf_token'];
}
function csrf_validar($token) {
    return is_string($token) && !empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
function exigir_sesion() {
    if (empty($_SESSION['administrador_id'])) { header('Location: login.php'); exit; }
}
function exigir_cliente() {
    if (empty($_SESSION['cliente_id'])) { header('Location: login.php?next=checkout'); exit; }
}
function obtener_categorias($conexion) { $resultado=$conexion->query("SELECT id,nombre FROM categorias ORDER BY CASE WHEN LOWER(TRIM(nombre)) LIKE '%vestido%' THEN 0 ELSE 1 END, nombre ASC"); return $resultado ? $resultado : false; }
