<?php
ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');
error_reporting(E_ALL);

header('Content-Type: application/json');

require_once __DIR__ . '/../config/mongo.php';
require_once __DIR__ . '/../config/redis.php';


$method = $_SERVER['REQUEST_METHOD'];
$jsonBody = json_decode(file_get_contents('php://input'), true) ?: [];
$sessionKey = $_REQUEST['session_key'] ?? ($jsonBody['session_key'] ?? '');

if (!$sessionKey) {
    echo json_encode(["status" => "error", "message" => "Session missing"]);
    exit;
}

$user_id = $redis->get("session:$sessionKey");

if (!$user_id) {
    echo json_encode(["status" => "error", "message" => "Invalid session"]);
    exit;
}

try {

    if ($method === 'GET') {

        $profile = $profileCollection->findOne([
            'user_id' => (int)$user_id
        ]);

        $profileData = null;
        if ($profile) {
            $profileData = $profile->getArrayCopy();
            unset($profileData['_id']);
        }

        echo json_encode([
            "status" => "success",
            "data" => $profileData
        ]);
        exit;
    }

    if ($method === 'POST') {

        $age = $_POST['age'] ?? ($jsonBody['age'] ?? '');
        $dob = $_POST['dob'] ?? ($jsonBody['dob'] ?? '');
        $contact = $_POST['contact'] ?? ($jsonBody['contact'] ?? '');
        $address = $_POST['address'] ?? ($jsonBody['address'] ?? '');
        $gender = $_POST['gender'] ?? ($jsonBody['gender'] ?? '');
        $designation = $_POST['designation'] ?? ($jsonBody['designation'] ?? '');
        $company = $_POST['company'] ?? ($jsonBody['company'] ?? '');

        if (!$age || !$dob || !$contact || !$address || !$gender || !$designation || !$company) {
            echo json_encode([
                "status" => "error",
                "message" => "All fields are required"
            ]);
            exit;
        }

        $age = (int)$age;

        $profileCollection->updateOne(
            ['user_id' => (int)$user_id],
            ['$set' => [
                'user_id' => (int)$user_id,
                'age' => $age,
                'dob' => $dob,
                'contact' => $contact,
                'address' => $address,
                'gender' => $gender, 
                'designation'=>$designation,
                'company'=>$company,
                'updated_at' => date("Y-m-d H:i:s")
            ]],
            ['upsert' => true]
        );

        echo json_encode([
            "status" => "success",
            "message" => "Profile saved successfully"
        ]);
        exit;
    }

    http_response_code(405);
    echo json_encode([
        "status" => "error",
        "message" => "Method not allowed"
    ]);
    exit;

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Server error",
        "debug" => $e->getMessage()
    ]);
    exit;
}
