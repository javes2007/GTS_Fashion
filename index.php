<?php
require_once __DIR__ . '/conectar.php';
$db = conexion();
$filtro = isset($_GET['categoria']) ? (int)$_GET['categoria'] : 0;
$categorias = obtener_categorias($db);
if ($filtro > 0) {
    $stmt = $db->prepare('SELECT p.id,p.nombre,p.descripcion,p.precio,p.imagen,p.stock,c.nombre AS categoria FROM productos p LEFT JOIN categorias c ON c.id=p.categoria_id WHERE p.categoria_id=? AND p.stock>0 ORDER BY CASE WHEN LOWER(COALESCE(c.nombre,'')) LIKE '%vestid%' THEN 0 ELSE 1 END, p.id DESC');
    $stmt->bind_param('i', $filtro);
} else {
    $stmt = $db->prepare('SELECT p.id,p.nombre,p.descripcion,p.precio,p.imagen,p.stock,c.nombre AS categoria FROM productos p LEFT JOIN categorias c ON c.id=p.categoria_id WHERE p.stock>0 ORDER BY CASE WHEN LOWER(COALESCE(c.nombre,'')) LIKE '%vestid%' THEN 0 ELSE 1 END, p.id DESC');
}
$stmt->execute(); $productos = $stmt->get_result();
$titulo = 'Tu estilo, tu esencia'; require __DIR__ . '/includes/encabezado.php';
?>
<main class="contenido tienda">
    <section id="catalogo" class="catalogo">
        <div class="filtros"><a href="index.php" class="<?php echo $filtro === 0 ? 'activo' : ''; ?>">Todo</a>
        <?php if ($categorias): while ($c = $categorias->fetch_assoc()): ?>
            <a href="index.php?categoria=<?php echo (int)$c['id']; ?>" class="<?php echo $filtro === (int)$c['id'] ? 'activo' : ''; ?>"><?php echo e($c['nombre']); ?></a>
        <?php endwhile; endif; ?></div>
        <?php if ($productos->num_rows === 0): ?><p class="vacio">Todavía no hay productos disponibles en esta categoría.</p>
        <?php else: ?><div class="productos">
            <?php while ($p = $productos->fetch_assoc()): ?><article class="producto">
                <div class="imagen-producto"><?php echo imagen_prenda($p['imagen'], $p['nombre']); ?></div>
                <div class="informacion-producto">
                    <span class="etiqueta"><?php echo e($p['categoria'] ?? 'Sin categoría'); ?></span>
                    <h3><?php echo e($p['nombre']); ?></h3><p><?php echo e($p['descripcion']); ?></p>
                    <p class="precio"><?php echo e(precio_texto($p['precio'])); ?></p>
                    <p class="disponibilidad"><?php echo (int)$p['stock']; ?> disponibles</p>
                    <form method="post" action="carrito.php" class="agregar-carrito"><input type="hidden" name="csrf" value="<?php echo e(csrf_token());?>">
                        <input type="hidden" name="accion" value="agregar"><input type="hidden" name="producto_id" value="<?php echo (int)$p['id']; ?>">
                        <label class="sr-only" for="cantidad-<?php echo (int)$p['id']; ?>">Cantidad</label>
                        <input id="cantidad-<?php echo (int)$p['id']; ?>" type="number" name="cantidad" min="1" max="<?php echo (int)$p['stock']; ?>" value="1">
                        <button class="boton" type="submit">Agregar al carrito</button>
                    </form>
                </div>
            </article><?php endwhile; ?>
        </div><?php endif; ?>
    </section>
</main>
<?php $stmt->close(); $db->close(); require __DIR__ . '/includes/pie.php'; ?>
