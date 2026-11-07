<?php
require_once __DIR__.'/verificar_sesion.php';$id=(int)($_GET['id']??$_POST['pedido_id']??0);$db=conexion();$error='';$mensaje='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 if(!csrf_validar($_POST['csrf']??''))$error='Formulario vencido. Recarga e inténtalo de nuevo.';
 else{
  $accion=$_POST['accion']??'';$detalle_id=(int)($_POST['detalle_id']??0);$producto_id=(int)($_POST['producto_id']??0);$cantidad=(int)($_POST['cantidad']??0);
  if($cantidad<1||$cantidad>99)$error='La cantidad debe estar entre 1 y 99.';
  else{
   $db->begin_transaction();
   try{
    $lock=$db->prepare('SELECT estado FROM pedidos WHERE id=? FOR UPDATE');$lock->bind_param('i',$id);$lock->execute();$pedido_lock=$lock->get_result()->fetch_assoc();$lock->close();if(!$pedido_lock)throw new Exception('Pedido no encontrado.');
    $activo=$pedido_lock['estado']!=='Cancelado';
    if($accion==='actualizar'){
     $s=$db->prepare('SELECT producto_id,cantidad FROM detalle_pedido WHERE id=? AND pedido_id=? FOR UPDATE');$s->bind_param('ii',$detalle_id,$id);$s->execute();$old=$s->get_result()->fetch_assoc();$s->close();if(!$old)throw new Exception('Línea de pedido no encontrada.');
     $delta=$cantidad-(int)$old['cantidad'];$pid=(int)$old['producto_id'];
     if($activo&&$delta>0){$s=$db->prepare('UPDATE productos SET stock=stock-? WHERE id=? AND stock>=?');$s->bind_param('iii',$delta,$pid,$delta);$s->execute();if($s->affected_rows!==1)throw new Exception('No hay stock suficiente para aumentar la cantidad.');$s->close();}
     elseif($activo&&$delta<0){$restaurar=abs($delta);$s=$db->prepare('UPDATE productos SET stock=stock+? WHERE id=?');$s->bind_param('ii',$restaurar,$pid);$s->execute();$s->close();}
     $s=$db->prepare('UPDATE detalle_pedido SET cantidad=? WHERE id=? AND pedido_id=?');$s->bind_param('iii',$cantidad,$detalle_id,$id);$s->execute();$s->close();
    }elseif($accion==='eliminar'){
     $s=$db->prepare('SELECT producto_id,cantidad FROM detalle_pedido WHERE id=? AND pedido_id=? FOR UPDATE');$s->bind_param('ii',$detalle_id,$id);$s->execute();$old=$s->get_result()->fetch_assoc();$s->close();if(!$old)throw new Exception('Línea de pedido no encontrada.');
     if($activo){$s=$db->prepare('UPDATE productos SET stock=stock+? WHERE id=?');$s->bind_param('ii',$old['cantidad'],$old['producto_id']);$s->execute();$s->close();}
     $s=$db->prepare('DELETE FROM detalle_pedido WHERE id=? AND pedido_id=?');$s->bind_param('ii',$detalle_id,$id);$s->execute();$s->close();
    }elseif($accion==='agregar'){
     $s=$db->prepare('SELECT nombre,precio,stock FROM productos WHERE id=? FOR UPDATE');$s->bind_param('i',$producto_id);$s->execute();$producto=$s->get_result()->fetch_assoc();$s->close();if(!$producto)throw new Exception('Producto no encontrado.');
     if($activo){$s=$db->prepare('UPDATE productos SET stock=stock-? WHERE id=? AND stock>=?');$s->bind_param('iii',$cantidad,$producto_id,$cantidad);$s->execute();if($s->affected_rows!==1)throw new Exception('Stock insuficiente.');$s->close();}
     $precio=(float)$producto['precio'];$s=$db->prepare('INSERT INTO detalle_pedido(pedido_id,producto_id,cantidad,precio) VALUES(?,?,?,?)');$s->bind_param('iiid',$id,$producto_id,$cantidad,$precio);$s->execute();$s->close();
    }else throw new Exception('Operación no válida.');
    $s=$db->prepare('UPDATE pedidos SET total=(SELECT COALESCE(SUM(cantidad*precio),0) FROM detalle_pedido WHERE pedido_id=?) WHERE id=?');$s->bind_param('ii',$id,$id);$s->execute();$s->close();
    $db->commit();$mensaje='Detalle actualizado. El total y el inventario se sincronizaron.';
   }catch(Throwable $e){$db->rollback();$error=$e->getMessage();}
  }
 }
}
$s=$db->prepare("SELECT p.*,CONCAT_WS(' ',NULLIF(c.nombre,''),NULLIF(c.apellidos,'')) AS cliente,c.correo FROM pedidos p INNER JOIN clientes c ON c.id=p.cliente_id WHERE p.id=?");$s->bind_param('i',$id);$s->execute();$pedido=$s->get_result()->fetch_assoc();$s->close();
if(!$pedido){$db->close();header('Location: pedidos.php');exit;}
$s=$db->prepare('SELECT d.id,d.producto_id,d.cantidad,d.precio,pr.nombre FROM detalle_pedido d LEFT JOIN productos pr ON pr.id=d.producto_id WHERE d.pedido_id=? ORDER BY d.id');$s->bind_param('i',$id);$s->execute();$items=$s->get_result();
$productos=$db->query('SELECT id,nombre,precio,stock FROM productos WHERE stock>0 ORDER BY nombre');
$titulo='Detalle del pedido';require __DIR__.'/includes/encabezado.php';
?>
<main class="contenido"><a href="pedidos.php">← Volver a pedidos</a><h1>Pedido #<?php echo $id;?></h1><?php if($mensaje):?><p class="aviso"><?php echo e($mensaje);?></p><?php endif;?><?php if($error):?><p class="error"><?php echo e($error);?></p><?php endif;?><p><strong>Cliente:</strong> <?php echo e($pedido['cliente']);?> · <?php echo e($pedido['correo']);?></p><p><strong>Fecha:</strong> <?php echo e($pedido['fecha']);?> · <strong>Estado:</strong> <?php echo e($pedido['estado']);?></p><p><strong>Entrega:</strong> <?php echo e($pedido['telefono_entrega']);?> · <?php echo e($pedido['direccion_entrega'].', '.$pedido['barrio'].', '.$pedido['ciudad'].', '.$pedido['departamento'].' '.$pedido['codigo_postal']);?></p><p><strong>Pago:</strong> <?php echo e($pedido['metodo_pago']);?><?php if($pedido['referencia_entrega']):?> · <strong>Referencia:</strong> <?php echo e($pedido['referencia_entrega']);?><?php endif;?></p>
<div class="tabla-scroll"><table><thead><tr><th>Producto</th><th>Cantidad</th><th>Precio unitario</th><th>Subtotal</th><th>Acciones</th></tr></thead><tbody><?php while($it=$items->fetch_assoc()):?><tr><td><?php echo e($it['nombre']??'Producto eliminado');?></td><td><form method="post" class="estado-pedido"><input type="hidden" name="csrf" value="<?php echo e(csrf_token());?>"><input type="hidden" name="pedido_id" value="<?php echo $id;?>"><input type="hidden" name="accion" value="actualizar"><input type="hidden" name="detalle_id" value="<?php echo (int)$it['id'];?>"><input type="number" name="cantidad" min="1" max="99" value="<?php echo (int)$it['cantidad'];?>"><button class="boton">Actualizar</button></form></td><td><?php echo e(precio_texto($it['precio']));?></td><td><?php echo e(precio_texto((int)$it['cantidad']*(float)$it['precio']));?></td><td><form method="post" onsubmit="return confirm('¿Quitar este producto del pedido?');"><input type="hidden" name="csrf" value="<?php echo e(csrf_token());?>"><input type="hidden" name="pedido_id" value="<?php echo $id;?>"><input type="hidden" name="accion" value="eliminar"><input type="hidden" name="detalle_id" value="<?php echo (int)$it['id'];?>"><input type="hidden" name="cantidad" value="1"><button class="boton boton-peligro">Quitar</button></form></td></tr><?php endwhile;?></tbody></table></div>
<section class="login-box"><h2>Agregar producto al pedido</h2><form method="post" class="formulario-acceso"><input type="hidden" name="csrf" value="<?php echo e(csrf_token());?>"><input type="hidden" name="pedido_id" value="<?php echo $id;?>"><input type="hidden" name="accion" value="agregar"><label>Producto<select name="producto_id" required><option value="">Selecciona un producto</option><?php while($p=$productos->fetch_assoc()):?><option value="<?php echo (int)$p['id'];?>"><?php echo e($p['nombre']);?> — <?php echo e(precio_texto($p['precio']));?> (stock <?php echo (int)$p['stock'];?>)</option><?php endwhile;?></select></label><label>Cantidad<input type="number" name="cantidad" min="1" max="99" value="1" required></label><button class="boton">Agregar al pedido</button></form></section>
<p><strong>Total del pedido: <?php echo e(precio_texto($pedido['total']));?></strong></p></main>
<?php $s->close();$db->close();require __DIR__.'/includes/pie.php';?>
