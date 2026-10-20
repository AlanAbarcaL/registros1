-- Importa en phpMyAdmin, seleccionando sae_escolar.
-- Seguro para volver a ejecutar; NO borra las calificaciones anteriores.
USE sae_escolar;
CREATE TABLE IF NOT EXISTS calificaciones_unidad (
 id_calificacion INT AUTO_INCREMENT PRIMARY KEY,
 id_asignacion INT NOT NULL,
 unidad TINYINT UNSIGNED NOT NULL,
 calificacion DECIMAL(5,2) NOT NULL,
 UNIQUE KEY unica_inscripcion_unidad (id_asignacion, unidad),
 CONSTRAINT fk_calif_inscripcion FOREIGN KEY (id_asignacion)
   REFERENCES asignacion_materia(id_asignacion) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
-- Las calificaciones antiguas siguen en asignacion_materia.calificacion.
-- No se asignan automáticamente a una unidad, porque no sabemos a cuál corresponden.
SELECT 'Tabla calificaciones_unidad lista' AS mensaje;
