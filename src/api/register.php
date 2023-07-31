<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

include_once '../database/connection.php';
$body = json_decode(file_get_contents('php://input'));

try {
    $email = $_POST["email"] ?? $body->email ?? null;
    $password = $_POST["password"] ?? $body->password ?? null;

    $stmt = $dbConn->prepare('SELECT email FROM users WHERE email = :email');
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $array = array('status' => 'failed', 'message' => 'Email already exists');
        echo json_encode($array);
    } else {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $dbConn->prepare('INSERT INTO users (email, password, name) VALUES (:email, :password, :email)');
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password_hash);
        $stmt->execute();
        $array = array('status' => 'true', 'message' => 'Email registered successfully, click continue to login');
        echo json_encode($array);
    }
} catch (Exception) {
    echo json_encode(array('status' => 'false', 'message' => $e->getMessage()));
}
