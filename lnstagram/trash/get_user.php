<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

include_once '../database/connection.php';
$body = json_decode(file_get_contents('php://input'));

$email = $_POST['email'] ?? $body->email ?? null;
$password = $_POST['password'] ?? $body->password ?? null;
if (isset($email)) {
    error_log('email: ' . $email . ' password: ' . $password);
    $stmt = $dbConn->query('SELECT * FROM users WHERE email = "' . $email . '" AND password = "' . $password . '"');
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($result) {
        echo json_encode($result);
    } else {
        echo json_encode(array('message' => 'User not found'));
    }
} else {
    echo json_encode(array('message' => 'User not found'));
}
