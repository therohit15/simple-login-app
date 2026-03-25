<?php
require_once __DIR__ . '/../config/db.php';

echo json_encode([
    "status" => "success",
    "message" => "Database connected successfully"
]);
