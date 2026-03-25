<?php

require_once __DIR__ . '/config/env.php';

$allowedOrigins = [
    'http://localhost:3000',
    'http://localhost:5173',
    'http://localhost:5500',
    'http://127.0.0.1:5500',
    'https://profile-managements.netlify.app',
    'https://simpledetails.netlify.app',
];

$allowedOriginsEnv = getenv('ALLOWED_ORIGINS');
if ($allowedOriginsEnv) {
    $allowedOriginsFromEnv = array_values(array_filter(array_map('trim', explode(',', $allowedOriginsEnv))));
    $allowedOrigins = array_values(array_unique(array_merge($allowedOrigins, $allowedOriginsFromEnv)));
}

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

if ($origin !== '') {
    $originNormalized = rtrim($origin, '/');
    $allowOrigin = false;

    // If ALLOWED_ORIGINS is not configured, fail-open for easier first deployment.
    if (!$allowedOriginsEnv) {
        $allowOrigin = true;
    } else {
        foreach ($allowedOrigins as $allowedOrigin) {
            $allowedOrigin = rtrim($allowedOrigin, '/');
            if ($allowedOrigin === '*' || strcasecmp($allowedOrigin, $originNormalized) === 0) {
                $allowOrigin = true;
                break;
            }
        }
    }

    if ($allowOrigin) {
        header("Access-Control-Allow-Origin: $origin");
    }
}

header('Vary: Origin');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

header('Content-Type: application/json');
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

switch ($uri) {
    case '/':
        echo json_encode(['status' => 'ok', 'message' => 'Backend is running']);
        break;

    case '/api/test':
        echo json_encode(['message' => 'API is working']);
        break;

    case '/api/signup':
    case '/api/register':
        require __DIR__ . '/api/register.php';
        break;

    case '/api/login':
        require __DIR__ . '/api/login.php';
        break;

    case '/api/profile':
        require __DIR__ . '/api/profile.php';
        break;

    case '/api/logout':
        require __DIR__ . '/api/logout.php';
        break;

    default:
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Route not found']);
}
