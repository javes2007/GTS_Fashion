<?php
require_once __DIR__.'/conectar.php'; exigir_cliente();$db=conexion();$id=(int)$_SESSION['cliente_id'];
$stmt=$db->prepare('SELECT id,fecha,total,estado,metodo_pago FROM pedidos WHERE cliente_id=? ORDER BY fecha DESC');$stmt->bind_param('i',$id);$stmt->execute();$pedidos=$stmt->get_result();
$titulo='Mis pedidos';require __DIR__.'/includes/encabezado.php';
?>
<main class="contenido"><h1>Mis pedidos</h1><?php if(isset($_GET['pedido'])):?><p class="aviso">Tu pedido #<?php echo (int)$_GET['pedido'];?> fue registrado correctamente. El pago es contra entrega.</p><?php endif;?>
<?php if(!$pedidos->num_rows):?><p>Aún no tienes pedidos.</p><a class="boton" href="index.php">Explorar tienda</a><?php else:?><div class="tabla-scroll"><table><thead><tr><th>Pedido</th><th>Fecha</th><th>Total</th><th>Estado</th><th>Pago</th></tr></thead><tbody><?php while($p=$pedidos->fetch_assoc()):?><tr><td>#<?php echo (int)$p['id'];?></td><td><?php echo e($p['fecha']);?></td><td><?php echo e(precio_texto($p['total']));?></td><td><?php echo e($p['estado']);?></td><td><?php echo e($p['metodo_pago']??'Contra entrega');?></td></tr><?php endwhile;?></tbody></table></div><?php endif;?></main>
<?php $stmt->close();$db->close();require __DIR__.'/includes/pie.php';?>
