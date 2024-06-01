Buenas Noches instructor. Para mas facilidad abajo dejo el script de la creación de la tabla en la base de datos.

// Nombre de la base de datos
USE FronBack;

CREATE TABLE records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT NOT NULL
);
