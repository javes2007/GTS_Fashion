-- GTS Fashion: habilita el inicio de sesión del administrador por correo.
-- Ejecutar UNA sola vez en phpMyAdmin, dentro de la base gts_fashion.
ALTER TABLE administradores
  ADD COLUMN correo VARCHAR(120) NULL AFTER usuario,
  ADD UNIQUE KEY uq_administradores_correo (correo);

-- Después, reemplaza el correo de ejemplo por el correo real del administrador:
-- UPDATE administradores SET correo='tu-correo@ejemplo.com' WHERE usuario='mati2205';
