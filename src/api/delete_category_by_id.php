<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

include_once '../database/connection.php';
$body = json_decode(file_get_contents('php://input'));

try {
    $id = $_POST['id'] ?? $body->id ?? null;
    $category = $dbConn->prepare("SELECT name FROM categories WHERE id = :id");
    $category->bindParam(':id', $id);
    $category->execute();
    if ($category->rowCount() > 0) {
        $product = $dbConn->prepare("SELECT name FROM products WHERE categoryId = :id");
        $product->bindParam(':id', $id);
        $product->execute();
        if ($product->rowCount() > 0) {
            $categoryRow = $category->fetch(PDO::FETCH_ASSOC);
            $array = array('status' => 'true', 'message' => 'failed', 'name' => $categoryRow['name']);
            echo json_encode($array);
            exit();
        } else {
            $stmt = $dbConn->prepare("DELETE FROM categories WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $array = array('status' => 'true', 'message' => 'success');
            echo json_encode($array);
        }
    } else {
        $array = array('status' => 'true', 'message' => 'failed');
        echo json_encode($array);
        exit();
    }
} catch (Exception $e) {
    echo json_encode(array('status' => 'false', 'message' => $e->getMessage()));
}
