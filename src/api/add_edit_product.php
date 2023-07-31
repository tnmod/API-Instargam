<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

include_once '../database/connection.php';
$body = json_decode(file_get_contents('php://input'));

try {
    $id = $_POST['id'] ?? $body->id ?? null;
    $name = $_POST['name'] ?? $body->name ?? null;
    $price = $_POST['price'] ?? $body->price ?? null;
    $image = $_POST['imageUpdoad'] ?? $body->imageUpdoad ?? null;
    $quantity = $_POST['quantity'] ?? $body->quantity ?? null;
    $categoryId = $_POST['categoryId'] ?? $body->categoryId ?? null;
    $description = $_POST['description'] ?? $body->description ?? null;
    //https://firebasestorage.googleapis.com/v0/b/api-php-image.appspot.com/o/images%2F9e47cb2f9dc74d9914d6.jpg?alt=media
    $image_uri = "";
    if ($image != null) {
        $image_uri = "https://firebasestorage.googleapis.com/v0/b/api-php-image.appspot.com/o/images%2F" . $image . "?alt=media";
    }
    if (isset($id)) {
        $sql = 'UPDATE products SET name = :name, price = :price, quantity = :quantity, image = :image, categoryId = :categoryId, description = :description
        WHERE id = :id';
        $stmt = $dbConn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':image', $image_uri);
        $stmt->bindParam(':categoryId', $categoryId);
        $stmt->bindParam(':description', $description);
        $stmt->execute();
        $array = array('status' => 'true', 'message' => 'updated', 'name' => $name, 'price' => $price, 'quantity' => $quantity, 'image' => $image_uri, 'categoryId' => $categoryId, 'description' => $description);
        echo json_encode($array);
    } else {
        $sql = 'INSERT INTO products(name, price, quantity, image, categoryId, description) 
                VALUES(:name, :price, :quantity, :image, :categoryId, :description)';
        $stmt = $dbConn->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':image', $image_uri);
        $stmt->bindParam(':categoryId', $categoryId);
        $stmt->bindParam(':description', $description);
        $stmt->execute();
        $array = array('status' => 'true', 'message' => 'added', 'name' => $name, 'price' => $price, 'quantity' => $quantity, 'image' => $image_uri, 'categoryId' => $categoryId, 'description' => $description);
        echo json_encode($array);
    };
} catch (Exception $e) {
    echo json_encode(array('status' => 'false', 'message' => $e->getMessage()));
}
