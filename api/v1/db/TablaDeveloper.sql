-- Tablas del sistema (Nuevas tablas para la version 4.1 moodle)

-- mdl_developer → Almacena los programadores externos.
-- mdl_developer_permissions → Define los permisos de cada programador.
-- mdl_developer_tokens → Controla los accesos con autenticación por tokens.

-- Table  mdl mdl_developer

CREATE TABLE mdl_developer (
    id BIGINT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,  -- Guardar en formato hash
    email VARCHAR(255) NOT NULL UNIQUE,
    status ENUM('active', 'suspended', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table mdl_developer_permissions

CREATE TABLE mdl_developer_permissions (
    id BIGINT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    developer_id BIGINT(10) UNSIGNED NOT NULL,
    permission VARCHAR(255) NOT NULL,  -- Ejemplo: "read_users", "read_courses"
    FOREIGN KEY (developer_id) REFERENCES mdl_developer(id) ON DELETE CASCADE
);
 
 -- mdl_developer_tokens

CREATE TABLE mdl_developer_tokens (
    id BIGINT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    developer_id BIGINT(10) UNSIGNED NOT NULL,
    token VARCHAR(255) NOT NULL UNIQUE,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (developer_id) REFERENCES mdl_developer(id) ON DELETE CASCADE
);

-- INSERT  Table mdl_developer 
INSERT INTO mdl_developer (username, password, email, status) VALUES
('jhohan_dev', '$2y$10$eImG9/xC4bQcNH7/aCpG6ueCj3ztz6TL5qO7y5RmUkLbUw7iToC2m', 'juan@example.com', 'active'),
('doris_dev', '$2y$10$z6H9P0v93qK8kJLfDtvFPeX03Qyf2ZDEuhR69G5eQa6EJm1YHd3.a', 'maria@example.com', 'inactive'),
('diego_dev', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4y4uL68/6Vz68iFSsFk2JaSnsyf4ba.', 'pedro@example.com', 'inactive');

--INSERT Table mdl_developer_permissions
INSERT INTO mdl_developer_permissions (developer_id, permission) VALUES
(1, 'read_users'),   -- jhohan_dev puede leer usuarios
(1, 'read_courses'), -- jhohan_dev puede leer cursos
(2, 'read_users'),   -- doris_dev solo puede leer usuarios
(3, 'read_courses'); -- diego_dev solo puede leer cursos, pero está inactivo

-- INSERT table mdl_developer_tokens (Esta tabla guardara los token del programador el el timpo de expiracion de 60 segundos ).
INSERT INTO mdl_developer_tokens (developer_id, token, expires_at) VALUES
(1, 'abc123jhohan_dev', DATE_ADD(NOW(), INTERVAL 60 SECOND)),  
(2, 'xyz456jhohan_dev', DATE_ADD(NOW(), INTERVAL 60 SECOND)); 

-- SELECT Table mdl_developer ( Donde su estado su usuario se jhohan_dev y su  se 'active') 
SELECT * FROM mdl_developer WHERE username = 'jhohan_dev' AND status = 'active';

-- SELECT Teble mdl_developer_tokens  ( Si token es igual y si experacion es mayor de nueva )
SELECT * FROM mdl_developer_tokens WHERE token = 'abc123juan' AND expires_at > NOW();

-- SELECT Table mdl_developer_permissions ( Buscara todos los permisos donde el id:de jhohan y los permisos sean de leer)
SELECT * FROM mdl_developer_permissions 
WHERE developer_id = 1 AND permission = 'read_users';

-- UPDATE Table mdl_developer (Aqui podemos Actualizar el estado de actividad donde el id se 1)
UPDATE mdl_developer SET status = 'inactive' WHERE id = 1;
