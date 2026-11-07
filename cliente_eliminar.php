<?php
require_once __DIR__.'/verificar_sesion.php';
if($_SERVER['REQUEST_METHOD']!=='POST'||!csrf_validar($_POST['csrf']??'')){http_response_code(400);exit('Solicitud no válida.');}
$id=(int)($_POST['id']??0);$db=conexion();$s=$db->prepare('SELECT COUNT(*) n FROM pedidos WHERE cliente_id=?');$s->bind_param('i',$id);$s->execute();$n=(int)$s->get_result()->fetch_assoc()['n'];$s->close();
if($n>0){$db->close();header('Location: clientes.php?msg='.urlencode('No se puede eliminar: el cliente tiene pedidos asociados y debe conservarse el historial.'));exit;}
$s=$db->prepare('DELETE FROM clientes WHERE id=?');$s->bind_param('i',$id);$ok=$s->execute()&&$s->affected_rows>0;$s->close();$db->close();header('Location: clientes.php?msg='.urlencode($ok?'Cliente eliminado.':'No se encontró el cliente.'));exit;
