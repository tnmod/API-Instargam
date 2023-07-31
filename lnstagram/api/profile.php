<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');


require '../../vendor/autoload.php';
include_once '../database/connection.php';
$body = json_decode(file_get_contents('php://input'));


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;

$id = $_GET['id'] ?? null;
$search_name = $_GET['search'] ?? null;
$emailResetPassword = $_POST['email'] ?? $body->email ?? null;

if (isset($id)) {
    try {
        $profile = $dbConn->prepare("SELECT user_id, username, full_name, avatar, bio FROM users WHERE user_id = :id");
        $profile->bindParam(':id', $id, PDO::PARAM_INT);
        $profile->execute();
        if ($profile->rowCount() > 0) {
            $sql = 'SELECT posts.post_id, posts.caption, posts.description, posts.image_url, posts.created_at, users.username, users.avatar FROM posts INNER JOIN users ON posts.user_id = users.user_id WHERE posts.user_id = :id';
            $posts = $dbConn->prepare($sql);
            $posts->bindParam(':id', $id, PDO::PARAM_INT);
            $posts->execute();
            if ($posts->rowCount() > 0) {
                $profileInfo = $profile->fetch(PDO::FETCH_ASSOC);
                $postsRow = $posts->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode(array('status' => '200', 'message' => 'OK', 'data' => ['user' => $profileInfo, 'post' => $postsRow]));
                exit();
            } else {
                echo json_encode(array('status' => '404', 'message' => 'Not Found', 'data' => []));
                exit();
            }
        } else {
            echo json_encode(array('status' => '404', 'message' => 'Not Found', 'data' => []));
            exit();
        }
    } catch (Exception $e) {
        echo json_encode(array('status' => '500', 'message' => $e->getMessage(), 'data' => []));
    }
} else if (isset($search_name)) {
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
} else if (isset($emailResetPassword)) {
    try {
        $sql = 'SELECT user_id,username FROM users WHERE email = :email';
        $stmt = $dbConn->prepare($sql);
        $stmt->bindParam(':email', $emailResetPassword);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            $user_id = $data['user_id'];
            $username = $data['username'];
            $sql = 'DELETE FROM tokens WHERE user_id = :user_id AND token_type = 1';
            $stmt = $dbConn->prepare($sql);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            $token = createToken($emailResetPassword, 1, (60 * 60));
            if ($token) {
                $sendEmail = sendMailCustom($emailResetPassword, $username, 'Reset password', $token);
                if ($sendEmail) {
                    $current_time = date('Y-m-d H:m:s', time());
                    $sql = 'INSERT INTO tokens(user_id,token,token_type, created_at) VALUE (:user_id,:token,1,:created_at)';
                    $stmt = $dbConn->prepare($sql);
                    $stmt->bindParam(':user_id', $user_id);
                    $stmt->bindParam(':token', $token);
                    $stmt->bindParam(':created_at', $current_time);
                    $stmt->execute();
                    echo json_encode(array('status' => '200', 'message' => 'OK', 'data' => []));
                } else {
                    echo json_encode(array('status' => '400', 'message' => 'Bad Request', 'data' => []));
                }
            } else {
                echo json_encode(array('status' => '400', 'message' => 'Bad Request', 'data' => []));
            }
        } else {
            echo json_encode(array('status' => '404', 'message' => 'Not Found', 'data' => []));
        }
    } catch (Exception $e) {
        echo json_encode(array('status' => '500', 'message' => $e->getMessage(), 'data' => []));
    }
} else {
    echo json_encode(array('status' => '400', 'message' => 'Bad Request', 'data' => []));
    exit();
}

function createToken($email, $token_type, $second)
{
    try {
        $secret_key = 'tokenreset';
        $expiration_time = time() + $second;
        $payload = array(
            "email" => $email,
            "token_type" => $token_type,
            "exp" => $expiration_time
        );
        $token = JWT::encode($payload, $secret_key, "HS256");
        return $token;
    } catch (ExpiredException $e) {
        return false;
    }
}

function sendMailCustom($email, $username, $title, $token)
{
    $mail = new PHPMailer(true);
    try {
        $mail->SMTPDebug = 0;
        $mail->SMTPAuth = true;
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->Username = 'tnmod2003@gmail.com';
        $mail->Password =  'beydtzuitvjiavkq';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('tnmod2003@gmail.com', 'AndroidNetwork');
        $mail->addAddress($email, $username);
        $mail->isHTML(true);
        $mail->Subject = $title;
        $mailBody =
            ' <html>
                <head>
                    <style>
                        body {
                            font-family: Arial, sans-serif;
                        }
                        .container {
                            max-width: 600px;
                            margin: 0 auto;
                            padding: 20px;
                            border: 1px solid #ddd;
                            border-radius: 5px;
                        }
                        .header {
                            background-color: #ffffff;
                            padding: 10px;
                            text-align: center;
                        }
                        .logo {
                            height: 100px;
                            width: auto;
                            background-color:#ffffff;
                        }
                        .content {
                            padding: 20px 0;
                        }
                        .button {
                            display: inline-block;
                            background-color: #cce4ff;
                            color: #fff;
                            padding: 10px 20px;
                            text-decoration: none;
                            border-radius: 5px;
                        }
                    </style>
                </head>
                <body>
                    <div class="container">
                        <div class="header">
                            <img src="https://fpt.edu.vn/Content/images/assets/Poly.png" alt="Your Company Logo" class="logo">
                        </div>
                        <div class="content">
                            <h2>Hello ' . $username . ',</h2>
                            <p>We received a request to reset your password. If you made this request, please click the button below to reset your password:</p>
                            <p><a href="http://127.0.0.1:3456/account.php?reset=' . $token . '" class="button">Reset Password</a></p>
                            <p>If you did not make this request, you can ignore this email.</p>
                            <p>Thank you,</p>
                            <p>FPT POLYTECHNIC</p>
                        </div>
                    </div>
                </body>
            </html>';
        $mail->Body = $mailBody;
        $mail->send();

        return true;
    } catch (\PHPMailer\PHPMailer\Exception $e) {
        return false;
    }
}
