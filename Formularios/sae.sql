CREATE DATABASE IF NOT EXISTS sae_escolar CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sae_escolar;

CREATE TABLE IF NOT EXISTS alumnos (
 id_alumno INT AUTO_INCREMENT PRIMARY KEY,
 matricula VARCHAR(20) NOT NULL UNIQUE,
 nombre VARCHAR(60) NOT NULL,
 apaterno VARCHAR(60) NOT NULL,
 amaterno VARCHAR(60) DEFAULT '',
 domicilio VARCHAR(150) DEFAULT '',
 correo VARCHAR(100) DEFAULT '',
 telefono VARCHAR(20) DEFAULT '',
 estatus ENUM('ALTA','BAJA') NOT NULL DEFAULT 'ALTA'
);
CREATE TABLE IF NOT EXISTS grupos (
 id_grupo INT AUTO_INCREMENT PRIMARY KEY,
 descripcion VARCHAR(50) NOT NULL UNIQUE,
 estatus ENUM('ACTIVO','INACTIVO') NOT NULL DEFAULT 'ACTIVO'
);
CREATE TABLE IF NOT EXISTS materias (
 id_materia INT AUTO_INCREMENT PRIMARY KEY,
 descripcion VARCHAR(100) NOT NULL UNIQUE
);
CREATE TABLE IF NOT EXISTS profesores (
 id_profesor INT AUTO_INCREMENT PRIMARY KEY,
 matricula VARCHAR(20) NOT NULL UNIQUE,
 numero_empleado VARCHAR(20) NOT NULL UNIQUE,
 nombre VARCHAR(60) NOT NULL,
 apaterno VARCHAR(60) NOT NULL,
 amaterno VARCHAR(60) DEFAULT ''
);
CREATE TABLE IF NOT EXISTS asignacion_grupo (
 id_asignacion INT AUTO_INCREMENT PRIMARY KEY,
 id_grupo INT NOT NULL,
 id_alumno INT NOT NULL UNIQUE,
 id_profesor INT NOT NULL,
 FOREIGN KEY (id_grupo) REFERENCES grupos(id_grupo),
 FOREIGN KEY (id_alumno) REFERENCES alumnos(id_alumno),
 FOREIGN KEY (id_profesor) REFERENCES profesores(id_profesor)
);
CREATE TABLE IF NOT EXISTS asignacion_materia (
 id_asignacion INT AUTO_INCREMENT PRIMARY KEY,
 id_materia INT NOT NULL,
 id_alumno INT NOT NULL,
 id_profesor INT NOT NULL,
 calificacion DECIMAL(5,2) NULL DEFAULT NULL,
 UNIQUE KEY alumno_materia (id_alumno, id_materia),
 FOREIGN KEY (id_materia) REFERENCES materias(id_materia),
 FOREIGN KEY (id_alumno) REFERENCES alumnos(id_alumno),
 FOREIGN KEY (id_profesor) REFERENCES profesores(id_profesor)
);

CREATE TABLE IF NOT EXISTS calificaciones_unidad (
 id_calificacion INT AUTO_INCREMENT PRIMARY KEY,
 id_asignacion INT NOT NULL,
 unidad TINYINT UNSIGNED NOT NULL,
 calificacion DECIMAL(5,2) NOT NULL,
 UNIQUE KEY unica_inscripcion_unidad (id_asignacion, unidad),
 FOREIGN KEY (id_asignacion) REFERENCES asignacion_materia(id_asignacion) ON DELETE CASCADE
);
