<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../config/redis.php';

$sessionKey = $_POST['session_key'] ?? '';

if ($sessionKey) {
    $redis->del("session:$sessionKey");
}

echo json_encode(["status" => "success"]);
