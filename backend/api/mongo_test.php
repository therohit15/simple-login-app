<?php
require_once "mongo.php";

try {
    $profileCollection->insertOne([
        "test" => "ok",
        "time" => date("Y-m-d H:i:s")
    ]);

    echo "MongoDB connected and insert successful";
} catch (Exception $e) {
    echo "MongoDB error: " . $e->getMessage();
}
