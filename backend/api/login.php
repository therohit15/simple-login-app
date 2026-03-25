<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/redis.php';


$data = json_decode(file_get_contents("php://input"), true);

$email = strtolower(trim($data["email"] ?? ""));
$password = $data["password"] ?? "";

if ($email === "" || $password === "") {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "message" => "Email and password are required"
    ]);
    exit;
}

// IMPORTANT: use `password`, not `password_hash`
$stmt = $pdo->prepare(
    "SELECT id, name, email, password 
     FROM users 
     WHERE email = :email"
);

$stmt->execute([
    ":email" => $email
]);

$user = $stmt->fetch();

if (!$user || !password_verify($password, $user["password"])) {
    http_response_code(401);
    echo json_encode([
        "status" => "error",
        "message" => "Invalid email or password"
    ]);
    exit;
}

/* ================= REDIS SESSION CREATION ================= */

$sessionKey = bin2hex(random_bytes(16)); // secure random key
$redis->setex("session:$sessionKey", 3600, $user["id"]); // 1 hour expiry

/* ========================================================== */

echo json_encode([
    "status" => "success",
    "message" => "Login successful",
    "session_key" => $sessionKey,
    "user" => [
        "id" => $user["id"],
        "name" => $user["name"],
        "email" => $user["email"]
    ]
]);
