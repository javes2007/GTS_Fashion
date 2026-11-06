<?php
require_once __DIR__.'/verificar_sesion.php';
header('Content-Type: application/json; charset=utf-8');header('Cache-Control: no-store, no-cache, must-revalidate');
$after=max(0,(int)($_GET['after']??0));$db=conexion();
$q=$db->query("SELECT COALESCE(MAX(id),0) ultimo, COALESCE(SUM(estado='Pendiente'),0) pendientes FROM pedidos");$stats=$q?$q->fetch_assoc():array('ultimo'=>0,'pendientes'=>0);
$stmt=$db->prepare("SELECT p.id,p.fecha,p.total,COALESCE(NULLIF(c.nombre,''),c.usuario,'Cliente') cliente FROM pedidos p LEFT JOIN clientes c ON c.id=p.cliente_id WHERE p.id>? ORDER BY p.id ASC LIMIT 10");
$stmt->bind_param('i',$after);$stmt->execute();$r=$stmt->get_result();$nuevos=array();while($row=$r->fetch_assoc())$nuevos[]=$row;
echo json_encode(array('ultimo'=>(int)$stats['ultimo'],'pendientes'=>(int)$stats['pendientes'],'nuevos'=>$nuevos),JSON_UNESCAPED_UNICODE);$stmt->close();$db->close();
