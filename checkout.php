<?php
require_once __DIR__.'/conectar.php'; exigir_cliente();
if(empty($_SESSION['carrito'])){header('Location: carrito.php');exit;}
$db=conexion(); $cliente_id=(int)$_SESSION['cliente_id'];
$stmt=$db->prepare('SELECT nombre,apellidos,documento,correo,telefono,direccion FROM clientes WHERE id=?');$stmt->bind_param('i',$cliente_id);$stmt->execute();$cliente=$stmt->get_result()->fetch_assoc();$stmt->close();
if(!$cliente){$db->close();session_destroy();header('Location: login.php');exit;}
$ids=array_map('intval',array_keys($_SESSION['carrito']));$lista=implode(',',array_filter($ids));$items=array();$total=0;
if($lista!==''){$res=$db->query("SELECT id,nombre,precio,stock FROM productos WHERE id IN ($lista)");while($p=$res->fetch_assoc()){$cant=min((int)$_SESSION['carrito'][$p['id']],(int)$p['stock']);if($cant>0){$p['cantidad']=$cant;$p['subtotal']=$cant*(float)$p['precio'];$items[]=$p;$total+=$p['subtotal'];}}}
if(!$items){$_SESSION['carrito']=array();$db->close();header('Location: carrito.php');exit;}
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    $telefono=trim($_POST['telefono']??'');$direccion=trim($_POST['direccion']??'');$barrio=trim($_POST['barrio']??'');$ciudad=trim($_POST['ciudad']??'');$departamento=trim($_POST['departamento']??'');$postal=trim($_POST['codigo_postal']??'');$referencia=trim($_POST['referencia']??'');
    if($telefono===''||$direccion===''||$barrio===''||$ciudad===''||$departamento===''||$postal==='')$error='Completa todos los datos obligatorios de entrega.';
    else {
        $db->begin_transaction();
        try {
            $check=$db->prepare('SELECT stock,precio FROM productos WHERE id=? FOR UPDATE');
            foreach($items as $it){$check->bind_param('i',$it['id']);$check->execute();$fresh=$check->get_result()->fetch_assoc();if(!$fresh||(int)$fresh['stock']<$it['cantidad'])throw new Exception('El stock de '.$it['nombre'].' cambió. Regresa al carrito y actualiza las cantidades.');}
            $check->close();
            $upc=$db->prepare('UPDATE clientes SET telefono=?,direccion=? WHERE id=?');$upc->bind_param('ssi',$telefono,$direccion,$cliente_id);$upc->execute();$upc->close();
            $pago='Contra entrega';$estado='Pendiente';
            $pedido=$db->prepare('INSERT INTO pedidos(cliente_id,total,estado,telefono_entrega,direccion_entrega,barrio,ciudad,departamento,codigo_postal,referencia_entrega,metodo_pago) VALUES(?,?,?,?,?,?,?,?,?,?,?)');
            $pedido->bind_param('idsssssssss',$cliente_id,$total,$estado,$telefono,$direccion,$barrio,$ciudad,$departamento,$postal,$referencia,$pago);$pedido->execute();$pedido_id=$pedido->insert_id;$pedido->close();
            $det=$db->prepare('INSERT INTO detalle_pedido(pedido_id,producto_id,cantidad,precio) VALUES(?,?,?,?)');
            $stock=$db->prepare('UPDATE productos SET stock=stock-? WHERE id=? AND stock>=?');
            foreach($items as $it){$pid=(int)$it['id'];$cant=(int)$it['cantidad'];$precio=(float)$it['precio'];$det->bind_param('iiid',$pedido_id,$pid,$cant,$precio);$det->execute();$stock->bind_param('iii',$cant,$pid,$cant);$stock->execute();if($stock->affected_rows!==1)throw new Exception('No se pudo reservar el stock de un producto.');}
            $det->close();$stock->close();$db->commit();$_SESSION['carrito']=array();$db->close();header('Location: mis_pedidos.php?pedido='.$pedido_id);exit;
        } catch(Throwable $e){$db->rollback();$error=$e->getMessage();}
    }
}
$titulo='Datos de entrega';require __DIR__.'/includes/encabezado.php';
?>
<main class="login-box checkout-box"><h1>Finalizar compra</h1><p>Confirma dónde recibirás tu pedido. El pago se realiza al momento de la entrega.</p>
<?php if($error):?><p class="error"><?php echo e($error);?></p><?php endif;?>
<div class="resumen-pedido"><strong>Resumen del pedido</strong><?php foreach($items as $it):?><p><?php echo e($it['nombre']);?> × <?php echo (int)$it['cantidad'];?> — <?php echo e(precio_texto($it['subtotal']));?></p><?php endforeach;?><strong>Total: <?php echo e(precio_texto($total));?></strong></div>
<form method="post" class="formulario-acceso">
<label>Nombre completo<input value="<?php echo e($cliente['nombre'].' '.$cliente['apellidos']);?>" disabled></label>
<label>Documento<input value="<?php echo e($cliente['documento']);?>" disabled></label>
<label>Correo electrónico<input value="<?php echo e($cliente['correo']);?>" disabled></label>
<label>Teléfono<input name="telefono" required maxlength="20" value="<?php echo e($_POST['telefono']??$cliente['telefono']);?>"></label>
<label>Dirección completa<input name="direccion" required maxlength="255" value="<?php echo e($_POST['direccion']??$cliente['direccion']);?>"></label>
<label>Barrio<input name="barrio" required maxlength="100" value="<?php echo e($_POST['barrio']??'');?>"></label>
<label>Ciudad<input name="ciudad" required maxlength="100" value="<?php echo e($_POST['ciudad']??'Montería');?>"></label>
<label>Departamento<input name="departamento" required maxlength="100" value="<?php echo e($_POST['departamento']??'Córdoba');?>"></label>
<label>Código postal<input name="codigo_postal" required maxlength="15" value="<?php echo e($_POST['codigo_postal']??'');?>"></label>
<label>Referencia para encontrar la dirección<textarea name="referencia" maxlength="500"><?php echo e($_POST['referencia']??'');?></textarea></label>
<label>Método de pago<input value="Contra entrega" disabled></label>
<button class="boton" type="submit">Confirmar pedido</button><a href="carrito.php">Volver al carrito</a>
</form></main>
<?php $db->close();require __DIR__.'/includes/pie.php';?>
