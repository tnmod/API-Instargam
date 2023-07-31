<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

include_once '../database/connection.php';
$body = json_decode(file_get_contents('php://input'));

try {

    $email = $_POST['email'] ?? $body->email ?? null;
    $password = $_POST['password'] ?? $body->password ?? null;
    $stmt = $dbConn->prepare('SELECT name,password,email FROM users WHERE email = :email');
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            if (password_verify($password, $user['password'])) {
                echo json_encode(array('status' => 'true', 'message' => 'Login successfully', 'name' => $user['name'], 'email' => $user['email'], 'password' => $user['password']));
            } else {
                echo json_encode(array('status' => 'false', 'message' => 'Email or password is incorrect'));
            }
        }
    } else {
        echo json_encode(array('status' => 'false', 'message' => 'Email or password is incorrect'));
    }
} catch (Exception $e) {
    echo json_encode(array('status' => 'false', 'message' => $e->getMessage()));
}
