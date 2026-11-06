-- Ejecutar una sola vez sobre la base de datos gts_fashion existente.
-- No elimina ni reemplaza productos, imágenes, clientes o pedidos.
ALTER TABLE pedidos
  ADD COLUMN telefono_entrega VARCHAR(20) NULL AFTER estado,
  ADD COLUMN direccion_entrega VARCHAR(255) NULL AFTER telefono_entrega,
  ADD COLUMN barrio VARCHAR(100) NULL AFTER direccion_entrega,
  ADD COLUMN ciudad VARCHAR(100) NULL AFTER barrio,
  ADD COLUMN departamento VARCHAR(100) NULL AFTER ciudad,
  ADD COLUMN codigo_postal VARCHAR(15) NULL AFTER departamento,
  ADD COLUMN referencia_entrega VARCHAR(500) NULL AFTER codigo_postal,
  ADD COLUMN metodo_pago VARCHAR(40) NOT NULL DEFAULT 'Contra entrega' AFTER referencia_entrega;
