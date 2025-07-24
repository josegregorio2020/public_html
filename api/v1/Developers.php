<?php
// Permitir solicitudes desde cualquier origen (ajustar según seguridad requerida)
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Incluir archivos necesarios de Moodle
require_once("../../config.php"); // Cargar configuración de Moodle
require_once "ConnectionDB.php";  // Incluir la conexión a la base de datos

global $DB, $USER;

$db = new Database();
$pdo = $db->getConnection();

$method = $_SERVER['REQUEST_METHOD'];

// Manejo de preflight request (CORS)
if ($method === 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {
    // 📌 Obtener el token desde la cabecera Authorization
    $headers = getallheaders();
    $token = isset($headers['Authorization']) ? trim(str_replace("Bearer ", "", $headers['Authorization'])) : null;

    // 📌 Validar si se envió el token
    if (!$token) {
        throw new Exception("Token no proporcionado.");
    }

    // 📌 Verificar si el token es válido en Moodle
    $user = validate_moodle_token($token);

    if (!$user) {
        throw new Exception("Token inválido o sesión expirada.");
    }

    // 📌 Verificar si el usuario es administrador
    if (!is_siteadmin($user->id)) {
        throw new Exception("Acceso denegado. Solo administradores pueden realizar esta acción.");
    }

    // 📌 Manejo de métodos HTTP
    if ($method === 'POST') { // Crear programador
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['username']) || !isset($data['password'])) {
            throw new Exception("Datos incompletos.");
        }

        $stmt = $pdo->prepare("INSERT INTO mdl_developer (username, password, status) VALUES (?, ?, 'active')");
        $stmt->execute([$data['username'], password_hash($data['password'], PASSWORD_BCRYPT)]);

        echo json_encode(["status" => "success", "message" => "Programador creado"]);
    } elseif ($method === 'GET') { // Listar programadores
        $stmt = $pdo->query("SELECT id, username, status FROM mdl_developer");
        echo json_encode(["status" => "success", "developers" => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
    } elseif ($method === 'PUT') { // Actualizar estado de un programador
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['id']) || !isset($data['status'])) {
            throw new Exception("ID o estado no proporcionados.");
        }

        $stmt = $pdo->prepare("UPDATE mdl_developer SET status = ? WHERE id = ?");
        $stmt->execute([$data['status'], $data['id']]);

        echo json_encode(["status" => "success", "message" => "Estado actualizado"]);
    } elseif ($method === 'DELETE') { // Eliminar programador
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['id'])) {
            throw new Exception("ID no proporcionado.");
        }

        $stmt = $pdo->prepare("DELETE FROM mdl_developer WHERE id = ?");
        $stmt->execute([$data['id']]);

        echo json_encode(["status" => "success", "message" => "Programador eliminado"]);
    } else {
        http_response_code(405);
        echo json_encode(["status" => "error", "message" => "Método no permitido"]);
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}

/**
 * 📌 Función para validar el token de Moodle
 */
function validate_moodle_token($token)
{
    global $DB;

    // Consultar en la tabla de tokens de Moodle si el token es válido
    $userToken = $DB->get_record("external_tokens", ["token" => $token]);

    if (!$userToken) {
        return false;
    }

    // Obtener información del usuario
    $user = $DB->get_record("user", ["id" => $userToken->userid]);

    return $user ?: false;
}
?>
