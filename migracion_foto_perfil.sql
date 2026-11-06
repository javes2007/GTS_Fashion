-- Ejecutar una sola vez en la base de datos gts_fashion.
ALTER TABLE clientes ADD COLUMN foto_perfil VARCHAR(255) NULL AFTER telefono;
