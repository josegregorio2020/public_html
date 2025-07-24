<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

require_once "ConnectionDB.php"; // Conexión a la BD

$db = new Database();
$pdo = $db->getConnection();

try {
    // 🔹 Validar token
    $headers = getallheaders();
    if (!isset($headers["Authorization"])) {
        echo json_encode(["status" => "error", "message" => "Token requerido"]);
        exit;
    }
    $token = trim(str_replace("Bearer", "", $headers["Authorization"]));

    $stmt = $pdo->prepare("SELECT developer_id FROM mdl_developer_tokens WHERE token = ? AND expires_at > NOW()");
    $stmt->execute([$token]);
    if (!$stmt->fetch()) {
        echo json_encode(["status" => "error", "message" => "Token inválido"]);
        exit;
    }

    // 🔹 Verificar si se solicita paginación
    if (isset($_GET['page'])) {
        $perPage = 10; // Número de registros por página
        $page = (int) $_GET['page'];
        if ($page < 1) $page = 1;
        $offset = ($page - 1) * $perPage;

        // 🔹 Contar total de registros en la vista
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM vista_usuarios_detallada");
        $totalUsers = $stmt->fetch(PDO::FETCH_ASSOC)["total"];
        $totalPages = ceil($totalUsers / $perPage);

        // 🔹 Obtener registros paginados de la vista
        $stmt = $pdo->prepare("SELECT * FROM vista_usuarios_detallada LIMIT ? OFFSET ?");
        $stmt->bindValue(1, $perPage, PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, PDO::PARAM_INT);
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            "status" => "success",
            "totalUsers" => $totalUsers,
            "totalPages" => $totalPages,
            "currentPage" => $page,
            "usersPerPage" => $perPage,
            "users" => $users
        ], JSON_UNESCAPED_UNICODE);
    } else {
        // 🔹 Listar todos los registros de la vista
        $stmt = $pdo->query("SELECT * FROM vista_usuarios_detallada");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(["status" => "success", "users" => $users], JSON_UNESCAPED_UNICODE);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Error interno", "details" => $e->getMessage()]);
}
?>
