<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

include_once '../database/connection.php';

try {
    $productAll = $dbConn->prepare("SELECT products.id, products.name,  products.price, products.description, products.image, products.categoryId as categoryId, categories.name as categoryName FROM products INNER JOIN categories ON products.categoryId = categories.id;");
    $productAll->execute();
    $productAllRow = $productAll->fetchAll(PDO::FETCH_ASSOC);
    if ($productAll->rowCount() > 0) {
        $array = array('status' => 'true', 'message' => 'success', 'data' => $productAllRow);
        echo json_encode($array);
    }
} catch (Exception $e) {
    echo json_encode(array('status' => 'false', 'message' => $e->getMessage()));
}
