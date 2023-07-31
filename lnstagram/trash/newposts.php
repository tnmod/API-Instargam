<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

include_once '../database/connection.php';
$body = json_decode(file_get_contents('php://input'));

try {
    $user_id = $_POST['user_id'] ?? $body->user_id ?? null;
    $caption = $_POST['caption'] ?? $body->caption ?? null;
    $description = $_POST['description'] ?? $body->description ?? null;
    $image_url = $_POST['image_url'] ?? $body->image_url ?? null;
    $created_at = date('Y-m-d H:i:s');


    $sql = 'INSERT INTO posts (user_id,caption, description, image_url, created_at) VALUES (:user_id, :caption, :description, :image_url, :created_at)';
    $stmt = $dbConn->prepare($sql);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':caption', $caption);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':image_url', $image_url);
    $stmt->bindParam(':created_at', $created_at);
    $stmt->execute();

    $array = array('status' => '200', 'message' => 'OK');
    echo json_encode($array);

} catch (Exception $e) {
    echo json_encode(array('status' => '500', 'message' => $e->getMessage()));
}
