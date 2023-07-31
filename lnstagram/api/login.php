<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

require __DIR__ . '../../../vendor/autoload.php';

use \Firebase\JWT\JWT;
use Firebase\JWT\Key;

include_once '../database/connection.php';
$body = json_decode(file_get_contents('php://input'));

$token = $_POST['token'] ?? $body->token ?? null;
$email = $_POST['email'] ?? $body->email ?? null;
$password = $_POST['password'] ?? $body->password ?? null;

try {
    if (isset($token)) {
        $secret_key = 'tokenemail';
        $decoded = JWT::decode($token, new Key($secret_key, 'HS256'));
        $current_time = time();
        if ($decoded->exp >= $current_time) {
            $email = $decoded->email;
            $password = $decoded->password;
        }
    }
    $stmt = $dbConn->prepare('SELECT username, password, email, full_name, avatar, bio FROM users WHERE email = :email');
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            if (password_verify($password, $user['password'])) {
                echo json_encode(array('status' => '200', 'message' => 'OK', 'data' => $user));
            } else {
                echo json_encode(array('status' => '401', 'message' => 'Unauthorized', 'data' => []));
            }
        }
    } else {
        echo json_encode(array('status' => '404', 'message' => 'Not Found', 'data' => []));
    }
} catch (Exception $e) {
    echo json_encode(array('status' => '500', 'message' => $e->getMessage(), 'data' => []));
}

