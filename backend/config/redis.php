<?php
require_once __DIR__ . '/env.php';
require_once __DIR__ . '/../vendor/autoload.php';

$redisUrl = getenv('REDIS_URL');

if (!$redisUrl) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'REDIS_URL not set in environment'
    ]);
    exit;
}

try {
    // Predis handles rediss:// URLs cleanly for Upstash TLS endpoints.
    $redis = new Predis\Client($redisUrl, [
        'timeout' => 5,
        'read_write_timeout' => 5,
    ]);

    $redis->ping();
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Redis connection failed',
        'debug' => $e->getMessage()
    ]);
    exit;
}
