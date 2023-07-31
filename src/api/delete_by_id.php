<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

include_once '../database/connection.php';
$body = json_decode(file_get_contents('php://input'));

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $_POST['id'] ?? $body->id ?? null;
        $check = $dbConn->prepare('SELECT id FROM products WHERE id = :id');
        $check->bindParam(':id', $id);
        $check->execute();
        if ($check->rowCount() > 0) {
            $stmt = $dbConn->prepare("DELETE FROM products WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $array = array('status' => 'true', 'message' => 'success');
            echo json_encode($array);
        } else {
            $array = array('status' => 'false', 'message' => 'failed');
            echo json_encode($array);
            exit();
        }
    }
} catch (Exception $e) {
    echo json_encode(array('status' => 'false', 'message' => $e->getMessage()));
}
