-- GTS Fashion: migración de esquema para instalaciones existentes.
-- Compatible con MariaDB 10.11+. Haz una copia de seguridad antes de ejecutarla.
-- Puede ejecutarse más de una vez: utiliza IF NOT EXISTS.

ALTER TABLE administradores
  ADD COLUMN IF NOT EXISTS correo VARCHAR(120) NULL AFTER usuario;
CREATE UNIQUE INDEX IF NOT EXISTS uq_administradores_correo ON administradores(correo);

ALTER TABLE clientes
  ADD COLUMN IF NOT EXISTS usuario VARCHAR(30) NULL AFTER id,
  ADD COLUMN IF NOT EXISTS foto_perfil VARCHAR(255) NULL AFTER telefono;
UPDATE clientes SET usuario=CONCAT('cliente',id) WHERE usuario IS NULL OR usuario='';
ALTER TABLE clientes
  MODIFY usuario VARCHAR(30) NOT NULL,
  MODIFY nombre VARCHAR(100) NULL,
  MODIFY apellidos VARCHAR(100) NULL DEFAULT '',
  MODIFY documento VARCHAR(30) NULL,
  MODIFY correo VARCHAR(120) NULL,
  MODIFY telefono VARCHAR(20) NULL,
  MODIFY direccion VARCHAR(255) NULL;
CREATE UNIQUE INDEX IF NOT EXISTS uq_clientes_usuario ON clientes(usuario);

ALTER TABLE pedidos
  ADD COLUMN IF NOT EXISTS telefono_entrega VARCHAR(20) NULL,
  ADD COLUMN IF NOT EXISTS direccion_entrega VARCHAR(255) NULL,
  ADD COLUMN IF NOT EXISTS barrio VARCHAR(100) NULL,
  ADD COLUMN IF NOT EXISTS ciudad VARCHAR(100) NULL,
  ADD COLUMN IF NOT EXISTS departamento VARCHAR(100) NULL,
  ADD COLUMN IF NOT EXISTS codigo_postal VARCHAR(15) NULL,
  ADD COLUMN IF NOT EXISTS referencia_entrega VARCHAR(500) NULL,
  ADD COLUMN IF NOT EXISTS metodo_pago VARCHAR(40) NOT NULL DEFAULT 'Contra entrega';

-- Índices útiles para consultas del catálogo, pedidos e informes.
CREATE INDEX IF NOT EXISTS idx_pedidos_fecha_estado ON pedidos(fecha,estado);
CREATE INDEX IF NOT EXISTS idx_detalle_producto ON detalle_pedido(producto_id);
