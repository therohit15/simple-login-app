<?php
require_once __DIR__ . '/../config/redis.php';

$redis->set("test_key", "redis_working", 60);
echo $redis->get("test_key");
