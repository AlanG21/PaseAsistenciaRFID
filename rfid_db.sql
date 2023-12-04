CREATE DATABASE rfid_db;

USE rfid_db;

CREATE TABLE asistencias (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uid VARCHAR(50) NOT NULL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE alumnos (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    uid VARCHAR(50) NOT NULL UNIQUE,  -- Ensure each student's UID is unique
    username VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) DEFAULT NULL  -- Increased the length to 100 to accommodate longer emails
);

CREATE TABLE profesores (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    pin VARCHAR(50) NOT NULL,
    username VARCHAR(100) UNIQUE NOT NULL,
    email VARCHAR(100) DEFAULT NULL  -- Increased the length to 100 to accommodate longer emails
    password VARCHAR(255) NOT NULL,
);

CREATE TABLE administradores (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    username VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) DEFAULT NULL  -- Increased the length to 100 to accommodate longer emails
    password VARCHAR(255) NOT NULL,
);

CREATE TABLE tarjetas (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uid VARCHAR(50) NOT NULL UNIQUE,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE asistencias
ADD asistio TINYINT(1) DEFAULT 0 NOT NULL;

-- Sample insert command
INSERT INTO `administradores` (`id`, `nombre`, `apellido`, `username`, `password`, `tipo`) VALUES (NULL, 'Luis', 'Loredo', 'admin1', 'admin123', 'administrador');


CREATE TABLE horarios (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    profesor_id INT(6) UNSIGNED,
    dia_semana ENUM('Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'),
    hora_inicio TIME,
    hora_fin TIME,
    FOREIGN KEY (profesor_id) REFERENCES profesores(id)
);

CREATE TABLE grupos (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre_grupo VARCHAR(50) NOT NULL,
    profesor_id INT(6) UNSIGNED,
    FOREIGN KEY (profesor_id) REFERENCES profesores(id)
);

CREATE TABLE alumnos_grupos (
    alumno_id INT(6) UNSIGNED,
    grupo_id INT(6) UNSIGNED,
    PRIMARY KEY (alumno_id, grupo_id),
    FOREIGN KEY (alumno_id) REFERENCES alumnos(id),
    FOREIGN KEY (grupo_id) REFERENCES grupos(id)
);

ALTER TABLE horarios
ADD grupo_id INT(6) UNSIGNED,
ADD FOREIGN KEY (grupo_id) REFERENCES grupos(id);
ALTER TABLE grupos
DROP FOREIGN KEY grupos_ibfk_1,  -- Asume que este es el nombre de la clave foránea. Puede variar.
DROP COLUMN profesor_id;
ALTER TABLE grupos
ADD profesor_id INT(6) UNSIGNED,
ADD FOREIGN KEY (profesor_id) REFERENCES profesores(id);
ALTER TABLE `administradores`
ADD `pin` VARCHAR(50) DEFAULT NULL AFTER `email`;
