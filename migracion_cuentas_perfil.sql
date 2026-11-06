-- GTS Fashion: ejecutar una sola vez después de hacer una copia de seguridad.
-- Conserva los clientes y pedidos existentes. Los clientes antiguos reciben usuario cliente<ID>.
ALTER TABLE clientes
  ADD COLUMN usuario VARCHAR(30) NULL AFTER id;

UPDATE clientes SET usuario=CONCAT('cliente',id) WHERE usuario IS NULL OR usuario='';

ALTER TABLE clientes
  MODIFY usuario VARCHAR(30) NOT NULL,
  MODIFY nombre VARCHAR(100) NULL,
  MODIFY apellidos VARCHAR(100) NULL DEFAULT '',
  MODIFY documento VARCHAR(30) NULL,
  MODIFY correo VARCHAR(120) NULL,
  MODIFY telefono VARCHAR(20) NULL,
  MODIFY direccion VARCHAR(255) NULL,
  ADD UNIQUE KEY uq_clientes_usuario (usuario);
