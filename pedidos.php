<?php
require_once __DIR__.'/verificar_sesion.php';$db=conexion();$mensaje='';$error='';
$estados=array('Pendiente','Confirmado','En preparación','Enviado','Entregado','Cancelado');
if($_SERVER['REQUEST_METHOD']==='POST'){
 if(!csrf_validar($_POST['csrf']??''))$error='Formulario vencido. Recarga la página.';
 else{
  $id=(int)($_POST['pedido_id']??0);$nuevo=$_POST['estado']??'';
  if($id<1||!in_array($nuevo,$estados,true))$error='Pedido o estado no válido.';
  else{
   $db->begin_transaction();
   try{
    $s=$db->prepare('SELECT estado FROM pedidos WHERE id=? FOR UPDATE');$s->bind_param('i',$id);$s->execute();$pedido=$s->get_result()->fetch_assoc();$s->close();
    if(!$pedido)throw new Exception('No se encontró el pedido.');
    $anterior=$pedido['estado'];
    if($anterior!==$nuevo){
     if($nuevo==='Cancelado'&&$anterior!=='Cancelado'){
      $s=$db->prepare('SELECT producto_id,cantidad FROM detalle_pedido WHERE pedido_id=?');$s->bind_param('i',$id);$s->execute();$items=$s->get_result();$up=$db->prepare('UPDATE productos SET stock=stock+? WHERE id=?');
      while($it=$items->fetch_assoc()){$cant=(int)$it['cantidad'];$pid=(int)$it['producto_id'];$up->bind_param('ii',$cant,$pid);$up->execute();} $up->close();$s->close();
     }elseif($anterior==='Cancelado'&&$nuevo!=='Cancelado'){
      $s=$db->prepare('SELECT d.producto_id,d.cantidad,pr.stock,pr.nombre FROM detalle_pedido d INNER JOIN productos pr ON pr.id=d.producto_id WHERE d.pedido_id=? FOR UPDATE');$s->bind_param('i',$id);$s->execute();$items=$s->get_result();$filas=array();while($it=$items->fetch_assoc())$filas[]=$it;$s->close();
      foreach($filas as $it)if((int)$it['stock']<(int)$it['cantidad'])throw new Exception('Stock insuficiente para reactivar el pedido: '.$it['nombre'].'.');
      $up=$db->prepare('UPDATE productos SET stock=stock-? WHERE id=? AND stock>=?');foreach($filas as $it){$cant=(int)$it['cantidad'];$pid=(int)$it['producto_id'];$up->bind_param('iii',$cant,$pid,$cant);$up->execute();if($up->affected_rows!==1)throw new Exception('No se pudo reservar el inventario al reactivar el pedido.');}$up->close();
     }
     $up=$db->prepare('UPDATE pedidos SET estado=? WHERE id=?');$up->bind_param('si',$nuevo,$id);$up->execute();$up->close();
    }
    $db->commit();$mensaje='Estado del pedido actualizado. El inventario fue sincronizado cuando correspondía.';
   }catch(Throwable $e){$db->rollback();$error=$e->getMessage();}
  }
 }
}
$res=$db->query('SELECT p.id,p.fecha,p.total,p.estado,p.telefono_entrega,p.direccion_entrega,p.barrio,p.ciudad,p.departamento,p.codigo_postal,p.metodo_pago,CONCAT_WS(" ",NULLIF(c.nombre,""),NULLIF(c.apellidos,"")) AS cliente,c.correo FROM pedidos p INNER JOIN clientes c ON c.id=p.cliente_id ORDER BY p.fecha DESC');
$titulo='Pedidos';require __DIR__.'/includes/encabezado.php';
?>
<main class="contenido"><h1>Pedidos</h1><?php if($mensaje):?><p class="aviso"><?php echo e($mensaje);?></p><?php endif;?><?php if($error):?><p class="error"><?php echo e($error);?></p><?php endif;?>
<?php if(!$res->num_rows):?><p>Aún no hay pedidos registrados.</p><?php else:?><div class="tabla-scroll"><table><thead><tr><th>Pedido / cliente</th><th>Entrega</th><th>Total</th><th>Pago</th><th>Estado</th></tr></thead><tbody>
<?php while($p=$res->fetch_assoc()):?><tr><td><a href="pedido_detalle.php?id=<?php echo (int)$p['id'];?>">Ver #<?php echo (int)$p['id'];?></a> · <?php echo e($p['cliente']);?><br><?php echo e($p['correo']);?><br><small><?php echo e($p['fecha']);?></small></td><td><?php echo e($p['telefono_entrega']);?><br><?php echo e($p['direccion_entrega'].', '.$p['barrio'].', '.$p['ciudad'].', '.$p['departamento'].' '.$p['codigo_postal']);?></td><td><?php echo e(precio_texto($p['total']));?></td><td><?php echo e($p['metodo_pago']);?></td><td><form method="post" class="estado-pedido"><input type="hidden" name="csrf" value="<?php echo e(csrf_token());?>"><input type="hidden" name="pedido_id" value="<?php echo (int)$p['id'];?>"><select name="estado"><?php foreach($estados as $e):?><option value="<?php echo e($e);?>" <?php echo $p['estado']===$e?'selected':'';?>><?php echo e($e);?></option><?php endforeach;?></select><button class="boton" type="submit">Guardar</button></form></td></tr><?php endwhile;?></tbody></table></div><?php endif;?></main>
<?php $db->close();require __DIR__.'/includes/pie.php';?>
