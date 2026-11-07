<?php
require_once __DIR__.'/conectar.php';
if (!isset($_SESSION['carrito'])||!is_array($_SESSION['carrito'])) $_SESSION['carrito']=array();
$mensaje=''; $error='';
if ($_SERVER['REQUEST_METHOD']==='POST' && !csrf_validar($_POST['csrf']??'')) $error='Formulario vencido. Recarga e inténtalo de nuevo.';
if ($_SERVER['REQUEST_METHOD']==='POST' && csrf_validar($_POST['csrf']??'')) {
    $accion=$_POST['accion']??'';
    if ($accion==='agregar') {
        $id=(int)($_POST['producto_id']??0); $cantidad=max(1,(int)($_POST['cantidad']??1));
        $db=conexion(); $stmt=$db->prepare('SELECT stock FROM productos WHERE id=?'); $stmt->bind_param('i',$id); $stmt->execute(); $p=$stmt->get_result()->fetch_assoc(); $stmt->close(); $db->close();
        if (!$p || (int)$p['stock']<1) $error='Este producto ya no está disponible.';
        else { $actual=(int)($_SESSION['carrito'][$id]??0); $_SESSION['carrito'][$id]=min((int)$p['stock'],$actual+$cantidad); $mensaje='Producto agregado al carrito.'; }
    } elseif ($accion==='actualizar') {
        foreach (($_POST['cantidades']??array()) as $id=>$cantidad) { $id=(int)$id; $cantidad=(int)$cantidad; if($id>0){if($cantidad<1)unset($_SESSION['carrito'][$id]);else $_SESSION['carrito'][$id]=min(99,$cantidad);} }
        $mensaje='Carrito actualizado.';
    } elseif ($accion==='quitar') { unset($_SESSION['carrito'][(int)($_POST['producto_id']??0)]); $mensaje='Producto retirado del carrito.'; }
    elseif ($accion==='cancelar') { $_SESSION['carrito']=array(); $mensaje='La compra fue cancelada y el carrito quedó vacío.'; }
}
$items=array(); $total=0;
if ($_SESSION['carrito']) {
    $db=conexion(); $ids=array_map('intval',array_keys($_SESSION['carrito'])); $lista=implode(',',array_filter($ids));
    if($lista!==''){ $res=$db->query("SELECT id,nombre,precio,imagen,stock FROM productos WHERE id IN ($lista)"); while($p=$res->fetch_assoc()){ $cant=min((int)$_SESSION['carrito'][$p['id']],(int)$p['stock']); if($cant<1){unset($_SESSION['carrito'][$p['id']]);continue;} $_SESSION['carrito'][$p['id']]=$cant; $p['cantidad']=$cant; $p['subtotal']=$cant*(float)$p['precio']; $total+=$p['subtotal']; $items[]=$p; } }
    $db->close();
}
$titulo='Tu carrito'; require __DIR__.'/includes/encabezado.php';
?>
<main class="contenido compra-contenido"><h1>Tu carrito</h1>
<?php if($mensaje):?><p class="aviso"><?php echo e($mensaje);?></p><?php endif;?><?php if($error):?><p class="error"><?php echo e($error);?></p><?php endif;?>
<?php if(!$items):?><p class="vacio">Tu carrito está vacío.</p><a class="boton" href="index.php#catalogo">Seguir comprando</a>
<?php else:?>
<form method="post" class="tabla-scroll"><input type="hidden" name="csrf" value="<?php echo e(csrf_token());?>"><input type="hidden" name="accion" value="actualizar"><table><thead><tr><th>Producto</th><th>Precio</th><th>Cantidad</th><th>Subtotal</th><th></th></tr></thead><tbody>
<?php foreach($items as $p):?><tr><td class="carrito-producto"><?php echo imagen_prenda($p['imagen'],$p['nombre']);?><strong><?php echo e($p['nombre']);?></strong></td><td><?php echo e(precio_texto($p['precio']));?></td><td><input type="number" name="cantidades[<?php echo (int)$p['id'];?>]" min="0" max="<?php echo (int)$p['stock'];?>" value="<?php echo (int)$p['cantidad'];?>" aria-label="Cantidad"></td><td><?php echo e(precio_texto($p['subtotal']));?></td><td><button class="enlace-boton" form="quitar-<?php echo (int)$p['id'];?>">Quitar</button></td></tr><?php endforeach;?>
</tbody></table><button class="boton boton-secundario" type="submit">Actualizar carrito</button></form>
<?php foreach($items as $p):?><form id="quitar-<?php echo (int)$p['id'];?>" method="post"><input type="hidden" name="csrf" value="<?php echo e(csrf_token());?>"><input type="hidden" name="accion" value="quitar"><input type="hidden" name="producto_id" value="<?php echo (int)$p['id'];?>"></form><?php endforeach;?>
<div class="resumen-compra"><strong>Total: <?php echo e(precio_texto($total));?></strong><div class="fila-botones"><form method="post"><input type="hidden" name="csrf" value="<?php echo e(csrf_token());?>"><input type="hidden" name="accion" value="cancelar"><button class="boton boton-secundario" type="submit">Cancelar compra</button></form><a class="boton" href="<?php echo !empty($_SESSION['cliente_id'])?'checkout.php':'login.php?next=checkout';?>">Continuar con la compra</a></div></div>
<?php endif;?></main>
<?php require __DIR__.'/includes/pie.php';?>
