USE sae_escolar;
-- Ejecuta una sola vez en MySQL Workbench o phpMyAdmin.
-- No borra alumnos, materias ni inscripciones existentes.
ALTER TABLE asignacion_materia
ADD COLUMN calificacion DECIMAL(5,2) NULL DEFAULT NULL;
-- NULL significa pendiente de captura; 0 es una calificación válida.
