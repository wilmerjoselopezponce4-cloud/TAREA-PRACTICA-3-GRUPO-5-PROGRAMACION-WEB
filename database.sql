CREATE DATABASE IF NOT EXISTS pwatarea3_grupo5 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE pwatarea3_grupo5;
CREATE TABLE IF NOT EXISTS usuarios (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  creado TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
-- Crear cuentas de demostración con instalar.php en localhost.
-- Las contraseñas se generan con password_hash(), no se guardan en texto plano.
