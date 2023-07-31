<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

include_once '../database/connection.php';
$body = json_decode(file_get_contents('php://input'));

$id = $_POST['id'] ?? $body->id ?? null;

$check = $dbConn->prepare("SELECT name FROM products WHERE id = :id");
$check->bindParam(':id', $id);
$check->execute();

if (isset($id)) {
    if ($check->rowCount() < 1) {
        $array = array('status' => 'failed', 'message' => 'Product not found');
        echo json_encode($array);
        exit();
    } else {
        try {
            $sql = 'SELECT products.id, products.name, products.price, products.image, products.description, products.quantity, products.categoryId, categories.id FROM products INNER JOIN categories WHERE products.id = :id AND products.categoryId = categories.id';
            $stmt = $dbConn->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $array = array('status' => 'true', 'message' => 'success', 'data' => $stmt->fetch(PDO::FETCH_ASSOC));
            echo json_encode($array);
        } catch (Exception $e) {
            echo json_encode(array('status' => 'false', 'message' => $e->getMessage()));
        }
    }
}
