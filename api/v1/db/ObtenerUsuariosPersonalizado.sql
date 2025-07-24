
-- Realizamos las consultas sobre la informacion de registro de los usuarios
SELECT id, shortname FROM mdl_user_info_field;

-- La Vista devuelve la informacion personal del registro del usuario
CREATE OR REPLACE VIEW vista_usuarios_completa AS 
SELECT 
    -- Datos principales de usuario
    u.id AS "ID Usuario",
    u.username AS "Nombre de Usuario",
    u.firstname AS "Nombre",
    u.lastname AS "Apellidos",
    u.email AS "Correo Electrónico",
    u.auth AS "Método de Autenticación",
    u.confirmed AS "Cuenta Confirmada",
    u.policyagreed AS "Política Aceptada",
    u.deleted AS "Cuenta Eliminada",
    u.suspended AS "Cuenta Suspendida",
    u.mnethostid AS "ID Red Moodle",
    u.city AS "Ciudad",
    u.country AS "País",
    u.lang AS "Idioma Preferido",
    u.theme AS "Tema Preferido",
    u.timezone AS "Zona Horaria",
    u.firstaccess AS "Primer Acceso",
    FROM_UNIXTIME(u.timecreated) AS "Fecha de Registro",
    FROM_UNIXTIME(u.timemodified) AS "Última Modificación",
    FROM_UNIXTIME(u.lastlogin) AS "Último Inicio de Sesión",
    FROM_UNIXTIME(u.lastaccess) AS "Último Acceso",
    u.picture AS "ID de Imagen de Perfil",
    u.description AS "Descripción del Usuario",
    
    -- Información de contacto
    u.phone1 AS "Teléfono 1",
    u.phone2 AS "Teléfono 2",
    u.address AS "Dirección",
    u.institution AS "Institución",
    u.department AS "Departamento",

    -- Datos del formulario personalizado
    d.data AS "Documento de Identificación",
    c.data AS "Celular",
    b.data AS "Barrio",
    e.data AS "Edad",
    co.data AS "Comuna",
    cor.data AS "Corregimiento",
    g.data AS "Género",
    et.data AS "Etnia",
    dis.data AS "Discapacidad",
    s.data AS "Situación"

FROM mdl_user u
-- Unimos los datos personalizados según los fieldid asignados en mdl_user_info_field
LEFT JOIN mdl_user_info_data d ON u.id = d.userid AND d.fieldid = 2  -- Documento
LEFT JOIN mdl_user_info_data c ON u.id = c.userid AND c.fieldid = 3  -- Celular
LEFT JOIN mdl_user_info_data b ON u.id = b.userid AND b.fieldid = 4  -- Barrio
LEFT JOIN mdl_user_info_data e ON u.id = e.userid AND e.fieldid = 5  -- Edad
LEFT JOIN mdl_user_info_data co ON u.id = co.userid AND co.fieldid = 6  -- Comuna
LEFT JOIN mdl_user_info_data cor ON u.id = cor.userid AND cor.fieldid = 7  -- Corregimiento
LEFT JOIN mdl_user_info_data g ON u.id = g.userid AND g.fieldid = 9  -- Género
LEFT JOIN mdl_user_info_data et ON u.id = et.userid AND et.fieldid = 10  -- Etnia
LEFT JOIN mdl_user_info_data dis ON u.id = dis.userid AND dis.fieldid = 12  -- Discapacidad
LEFT JOIN mdl_user_info_data s ON u.id = s.userid AND s.fieldid = 13;  -- Situación

-- SELECT vista_usuarios_registro (Lista total de usuarios datos personales)
SELECT * FROM vista_usuarios_registro;


-- Vista que relaciona los usuarios con su actividad en moodle
CREATE OR REPLACE VIEW vista_usuarios_detallada AS
SELECT 
    u.id AS user_id,
    u.email,
    u.username AS usuario,
    u.city AS ciudad,
    u.country AS pais,
    u.lang AS idioma,
    u.timezone AS zona_horaria,
    u.description,

    -- Separar nombres y apellidos
    SUBSTRING_INDEX(u.firstname, ' ', 1) AS primer_nombre,
    IF(LOCATE(' ', u.firstname) > 0, SUBSTRING_INDEX(u.firstname, ' ', -1), '') AS segundo_nombre,
    SUBSTRING_INDEX(u.lastname, ' ', 1) AS primer_apellido,
    IF(LOCATE(' ', u.lastname) > 0, SUBSTRING_INDEX(u.lastname, ' ', -1), '') AS segundo_apellido,

    -- Campos personalizados
    d1.data AS tipodocumento,
    d2.data AS documento,
    d3.data AS celular,
    d4.data AS barrio,
    d5.data AS edad,
    d6.data AS comuna,
    d7.data AS corregimiento,
    d8.data AS fechanacimiento,
    d9.data AS ubicacion,
    d10.data AS direccion_personalizada,
    d11.data AS telefono_personalizado,
    d12.data AS genero,
    d13.data AS etnia,
    d14.data AS situacion,
    d15.data AS sexo,
    d16.data AS tipodiscapacidad,
    d17.data AS discapacidad,
    d18.data AS codigodane,
    d19.data AS niveldeestudios,
    d20.data AS estrato

FROM mdl_user u
LEFT JOIN mdl_user_info_data d1 ON u.id = d1.userid AND d1.fieldid = (SELECT id FROM mdl_user_info_field WHERE shortname = 'tipodocumento')
LEFT JOIN mdl_user_info_data d2 ON u.id = d2.userid AND d2.fieldid = (SELECT id FROM mdl_user_info_field WHERE shortname = 'documento')
LEFT JOIN mdl_user_info_data d3 ON u.id = d3.userid AND d3.fieldid = (SELECT id FROM mdl_user_info_field WHERE shortname = 'celular')
LEFT JOIN mdl_user_info_data d4 ON u.id = d4.userid AND d4.fieldid = (SELECT id FROM mdl_user_info_field WHERE shortname = 'barrio')
LEFT JOIN mdl_user_info_data d5 ON u.id = d5.userid AND d5.fieldid = (SELECT id FROM mdl_user_info_field WHERE shortname = 'edad')
LEFT JOIN mdl_user_info_data d6 ON u.id = d6.userid AND d6.fieldid = (SELECT id FROM mdl_user_info_field WHERE shortname = 'comuna')
LEFT JOIN mdl_user_info_data d7 ON u.id = d7.userid AND d7.fieldid = (SELECT id FROM mdl_user_info_field WHERE shortname = 'corregimiento')
LEFT JOIN mdl_user_info_data d8 ON u.id = d8.userid AND d8.fieldid = (SELECT id FROM mdl_user_info_field WHERE shortname = 'fechanacimiento')
LEFT JOIN mdl_user_info_data d9 ON u.id = d9.userid AND d9.fieldid = (SELECT id FROM mdl_user_info_field WHERE shortname = 'ubicacion')
LEFT JOIN mdl_user_info_data d10 ON u.id = d10.userid AND d10.fieldid = (SELECT id FROM mdl_user_info_field WHERE shortname = 'direccion')
LEFT JOIN mdl_user_info_data d11 ON u.id = d11.userid AND d11.fieldid = (SELECT id FROM mdl_user_info_field WHERE shortname = 'telefono')
LEFT JOIN mdl_user_info_data d12 ON u.id = d12.userid AND d12.fieldid = (SELECT id FROM mdl_user_info_field WHERE shortname = 'genero')
LEFT JOIN mdl_user_info_data d13 ON u.id = d13.userid AND d13.fieldid = (SELECT id FROM mdl_user_info_field WHERE shortname = 'etnia')
LEFT JOIN mdl_user_info_data d14 ON u.id = d14.userid AND d14.fieldid = (SELECT id FROM mdl_user_info_field WHERE shortname = 'situacion')
LEFT JOIN mdl_user_info_data d15 ON u.id = d15.userid AND d15.fieldid = (SELECT id FROM mdl_user_info_field WHERE shortname = 'sexo')
LEFT JOIN mdl_user_info_data d16 ON u.id = d16.userid AND d16.fieldid = (SELECT id FROM mdl_user_info_field WHERE shortname = 'tipodiscapacidad')
LEFT JOIN mdl_user_info_data d17 ON u.id = d17.userid AND d17.fieldid = (SELECT id FROM mdl_user_info_field WHERE shortname = 'discapacidad')
LEFT JOIN mdl_user_info_data d18 ON u.id = d18.userid AND d18.fieldid = (SELECT id FROM mdl_user_info_field WHERE shortname = 'codigodane')
LEFT JOIN mdl_user_info_data d19 ON u.id = d19.userid AND d19.fieldid = (SELECT id FROM mdl_user_info_field WHERE shortname = 'niveldeestudios')
LEFT JOIN mdl_user_info_data d20 ON u.id = d20.userid AND d20.fieldid = (SELECT id FROM mdl_user_info_field WHERE shortname = 'estrato');


-- SELECT vista_usuarios_detallada (Lista toda la informacion del usuario en el sistema moodle)
SELECT * FROM vista_usuarios_detallada WHERE email='gregoriomatrix@gmail.com';


SELECT * FROM mdl_user_preferences WHERE name = 'drawer-open-block';