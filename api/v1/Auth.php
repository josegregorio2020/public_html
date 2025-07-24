<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

require_once "ConnectionDB.php"; // Incluir la conexión

$db = new Database();
$pdo = $db->getConnection();

try {
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (!isset($data['username']) || !isset($data['password'])) {
        echo json_encode(["status" => "error", "message" => "Datos incompletos"]);
        exit;
    }

    $stmt = $pdo->prepare("SELECT id, password, status FROM mdl_developer WHERE username = ?");
    $stmt->execute([$data['username']]);
    $developer = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$developer || !password_verify($data['password'], $developer['password'])) {
        echo json_encode(["status" => "error", "message" => "Credenciales inválidas"]);
        exit;
    }

    if ($developer['status'] !== 'active') {
        echo json_encode(["status" => "error", "message" => "Acceso denegado"]);
        exit;
    }

    $token = hash_hmac("sha256", $developer['id'] . time(), "secreto_seguro");
    $expires_at = date("Y-m-d H:i:s", strtotime("+60 seconds"));

    $stmt = $pdo->prepare("INSERT INTO mdl_developer_tokens (developer_id, token, expires_at) VALUES (?, ?, ?)");
    $stmt->execute([$developer['id'], $token, $expires_at]);

    echo json_encode(["status" => "success", "token" => $token, "expires_at" => $expires_at]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Error interno"]);
}
?>
