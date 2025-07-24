-- Crea la tabla `dev_informacion` para información básica
CREATE TABLE dev_informacion (
    id INT AUTO_INCREMENT PRIMARY KEY,
    descripcion TEXT NOT NULL,
    telefono VARCHAR(20),
    whatsapp VARCHAR(20),
    correo VARCHAR(100),
    facebook VARCHAR(255),
    twitter VARCHAR(255),
    instagram VARCHAR(255),
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Crea la tabla `dev_preguntas_frecuentes` para preguntas frecuentes
CREATE TABLE dev_preguntas_frecuentes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pregunta VARCHAR(255) NOT NULL,
    respuesta TEXT NOT NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Crea la tabla `dev_archivos` para el gestor de archivos
CREATE TABLE dev_archivos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    ruta VARCHAR(255) NOT NULL,
    tipo VARCHAR(50) NOT NULL,
    tamano INT NOT NULL, -- Tamaño en bytes
    subido_por INT NOT NULL, -- Relación con el usuario que subió el archivo
    subido_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    descripcion TEXT
);


RENAME TABLE dev_informacion TO mdl_dev_informacion;
RENAME TABLE dev_preguntas_frecuentes TO mdl_dev_preguntas_frecuentes;
RENAME TABLE dev_archivos TO mdl_dev_archivos;