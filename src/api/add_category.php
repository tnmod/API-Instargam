<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

include_once '../database/connection.php';
$body = json_decode(file_get_contents('php://input'));

try {
    $name = $_POST['name'] ?? $body->name ?? null;
    if (isset($name)) {
        $check = $dbConn->prepare("SELECT name FROM categories WHERE name = :name");
        $check->bindParam(':name', $name);
        $check->execute();
        if ($check->rowCount() > 0) {
            $array = array('status' => 'true', 'message' => 'failed');
            echo json_encode($array);
            exit();
        } else {
            $sql = 'INSERT INTO categories(name) 
            VALUES(:name)';
            $stmt = $dbConn->prepare($sql);
            $stmt->bindParam(':name', $name);
            $stmt->execute();
            $array = array('status' => 'true', 'message' => 'success');
            echo json_encode($array);
        }
    } else {
        $array = array('status' => 'true', 'message' => 'failed');
        echo json_encode($array);
    };
} catch (Exception $e) {
    echo json_encode(array('status' => 'false', 'message' => $e->getMessage()));
}
