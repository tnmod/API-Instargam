<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST");
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

include_once '../database/connection.php';
$body = json_decode(file_get_contents('php://input'));

$post_id = $_GET['id'] ?? null;
$search_post = $_GET['search'] ?? null;

$user_id = $_POST['user'] ?? $body->user ?? null;

try {

    //create post
    if (isset($user_id)) {
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
        echo json_encode(array('status' => '200', 'message' => 'OK', 'data' => []));
    }
    //get detail post
    else if (isset($post_id)) {
        error_log($post_id);
        $sql = 'SELECT posts.post_id, posts.caption, posts.description, posts.image_url, posts.created_at, users.user_id as userid, users.username, users.avatar FROM posts INNER JOIN users ON posts.user_id = users.user_id WHERE posts.post_id = :post_id';
        $post = $dbConn->prepare($sql);
        $post->bindParam(':post_id', $post_id);
        $post->execute();
        // $array = array('status'=>200, 'message' => 'OK', 'data' => $commentAllRow);
        // echo json_encode($array);
        if ($post->rowCount() > 0) {

            $postAllRow = $post->fetchAll(PDO::FETCH_ASSOC);

            $objDetail = array('post' => $postAllRow);

            $sql1 = 'SELECT comments.comment_id, comments.user_id, comments.post_id, comments.content, comments.created_at FROM posts INNER JOIN comments ON posts.post_id = comments.post_id WHERE posts.post_id = :post_id';
            $comment = $dbConn->prepare($sql1);
            $comment->bindParam(':post_id', $post_id);
            $comment->execute();

            $sql2 = 'SELECT likes.like_id, likes.user_id, likes.post_id FROM posts INNER JOIN likes ON posts.post_id = likes.post_id WHERE posts.post_id = :post_id';
            $like = $dbConn->prepare($sql2);
            $like->bindParam(':post_id', $post_id);
            $like->execute();

            if ($comment->rowCount() > 0) {
                $commentAllRow = $comment->fetchAll(PDO::FETCH_ASSOC);
                $commentformat = ['comments' => $commentAllRow];
                $objDetail = array_merge($objDetail, $commentformat);
            } else {
                $commentformat = ['comments' => []];
                $objDetail = array_merge($objDetail, $commentformat);
            }

            if ($like->rowCount() > 0) {
                $likeAllRow = $like->fetchAll(PDO::FETCH_ASSOC);
                $likeformat = ['likes' => $likeAllRow];
                $objDetail = array_merge($objDetail, $likeformat);
            } else {
                $likeformat = ['likes' => []];
                $objDetail = array_merge($objDetail, $likeAllRow);
            }

            echo json_encode(array('status' => '200', 'data' => $objDetail));
        } else {
            echo json_encode(array('status' => '404', 'message' => 'Not Found', 'data' => []));
        }
    } else if (isset($search_post)) {
        try {
            $search_term = '%' . $search_name . '%';
            $sql = 'SELECT posts.post_id, posts.caption, posts.description, posts.image_url, posts.created_at, users.user_id as userid, users.username, users.avatar FROM posts INNER JOIN users ON posts.user_id = users.user_id WHERE posts.caption LIKE :search_term';
            $post = $dbConn->prepare($sql);
            $post->bindParam(':search_term', $search_term, PDO::PARAM_STR);
            $post->execute();

            if ($post->rowCount() > 0) {
                $postAllRow = $post->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode(array('status' => '200', 'message' => 'OK', 'data' => $postAllRow));
            } else {
                echo json_encode(array('status' => '404', 'message' => 'Not Found', 'data' => []));
                exit();
            }
        } catch (Exception $e) {
            echo json_encode(array('status' => '500', 'message' => $e->getMessage(), 'data' => []));
        }
    }

    //get all post
    else {
        $sql = 'SELECT posts.post_id, posts.caption, posts.description, posts.image_url, posts.created_at, users.username, users.avatar FROM posts INNER JOIN users ON posts.user_id = users.user_id';

        $stmt = $dbConn->prepare($sql);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $postAllRow = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(array('status' => '200', 'message' => 'OK', 'data' => $postAllRow));
            exit();
        } else {
            echo json_encode(array('status' => '404', 'message' => 'Not Found', 'data' => []));
        }
    }
} catch (Exception $e) {
    echo json_encode(array('status' => '500', 'message' => $e->getMessage(), 'data' => []));
}
