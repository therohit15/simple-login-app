<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/env.php';

use MongoDB\Client;

$mongoUri = getenv('MONGODB_URI') ?: getenv('MONGO_URI');
$mongoDatabase = getenv('MONGODB_DATABASE') ?: 'internship_app';

if (!$mongoUri) {
	throw new RuntimeException('MONGODB_URI (or MONGO_URI) is not set');
}

$client = new Client($mongoUri);

$database = $client->$mongoDatabase;
$profileCollection = $database->profiles;