<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST");
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

$reset_password = $_POST['password'] ?? $body->password ?? null;
$token = $_POST['token'] ?? $body->token ?? null;
$email = $_POST['email'] ?? $body->email ?? null;


if (isset($token) && !isset($reset_password)) {
    try {
        $sql = 'SELECT token FROM tokens WHERE token = :token';
        $stmt = $dbConn->prepare($sql);
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $secret_key = 'tokenemail';
            $decoded = JWT::decode($token, new Key($secret_key, 'HS256'));
            $current_time = time();
            if ($decoded->exp >= $current_time) {
                $email = $decoded->email;
                $token_type = $decoded->token_type;

                $sql = 'SELECT user_id FROM users WHERE email = :email';
                $stmt = $dbConn->prepare($sql);
                $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                $stmt->execute();

                $user_id = $stmt->fetch(PDO::FETCH_ASSOC)['user_id'];

                $sql = 'UPDATE users SET verified = 1 WHERE user_id = :user_id';
                $stmt = $dbConn->prepare($sql);
                $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $stmt->execute();

                $sql = 'DELETE FROM tokens WHERE user_id = :user_id AND token_type = 0';
                $stmt = $dbConn->prepare($sql);
                $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $stmt->execute();

                echo json_encode(array('status' => '200', 'message' => 'OK', 'data' => []));
            }
        } else {
            echo json_encode(array('status' => '404', 'message' => 'Token Not Found or Expired', 'data' => []));
        }
    } catch (ExpiredException $e) {
        if ($e->getMessage() === 'Expired token') {
            $sql = 'DELETE FROM tokens WHERE token = :token';
            $stmt = $dbConn->prepare($sql);
            $stmt->bindParam(':token', $token, PDO::PARAM_STR);
            $stmt->execute();
        }
        echo json_encode(array('status' => '500', 'message' => $e->getMessage(), 'data' => []));
    }
} else if (isset($email)) {
    try {
        $sql = 'SELECT user_id, username FROM users WHERE email = :email AND verified = 0';
        $stmt = $dbConn->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            $user_id = $data['user_id'];
            $username = $data['username'];
            $sql = 'DELETE FROM tokens WHERE user_id = :user_id AND token_type = 0';
            $stmt = $dbConn->prepare($sql);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            $secret_key = 'tokenemail';
            $expiration_time = time() + (60 * 60);
            $payload = array(
                "email" => $email,
                "token_type" => 0,
                "exp" => $expiration_time
            );
            $token = JWT::encode($payload, $secret_key, "HS256");
            $result = sendMailCustom($email, $username, 'Verify your account', $token);
            if ($result) {
                $current_time = date('Y-m-d H:i:s', time());
                $sql = 'INSERT INTO tokens(user_id,token,created_at, token_type) VALUE (:user_id, :token, :created_at, 0)';
                $stmt = $dbConn->prepare($sql);
                $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $stmt->bindParam(':token', $token, PDO::PARAM_STR);
                $stmt->bindParam(':created_at', $current_time, PDO::PARAM_STR);
                $stmt->execute();
                echo json_encode(array('status' => '200', 'message' => 'OK', 'data' => []));
            } else {
                echo json_encode(array('status' => '500', 'message' => 'Bad request', 'data' => []));
            }
        } else {
            echo json_encode($array = array('status' => '409', 'message' => 'conflict', 'data' => []));
        }
    } catch (Exception $e) {
        echo json_encode(array('status' => '500', 'message' => $e->getMessage(), 'data' => []));
    }
} else if (isset($token) && isset($reset_password)) {
    error_log($token);

    try {
        $sql = 'SELECT user_id FROM tokens WHERE token = :token';
        $stmt = $dbConn->prepare($sql);
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $secret_key = 'tokenreset';
            $decoded = JWT::decode($token, new Key($secret_key, 'HS256'));
            $current_time = time();
            if ($decoded->exp >= $current_time) {
                $email = $decoded->email;
                $token_type = $decoded->token_type;

                $sql = 'SELECT user_id FROM users WHERE email = :email';
                $stmt = $dbConn->prepare($sql);
                $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                $stmt->execute();

                $user_id = $stmt->fetch(PDO::FETCH_ASSOC)['user_id'];
                $password_hash = password_hash($reset_password, PASSWORD_DEFAULT);

                $sql = 'UPDATE users SET password = :password WHERE user_id = :user_id';
                $stmt = $dbConn->prepare($sql);
                $stmt->bindParam(':password', $password_hash, PDO::PARAM_STR);
                $stmt->bindParam(':user_id', $user_id);
                $stmt->execute();

                $sql = 'DELETE FROM tokens WHERE user_id = :user_id AND token_type = 1';
                $stmt = $dbConn->prepare($sql);
                $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $stmt->execute();

                echo json_encode(array('status' => '200', 'message' => 'OK', 'data' => []));
            }
        } else {
            echo json_encode(array('status' => '404', 'message' => 'Token Not Found or Expired', 'data' => []));
        }
    } catch (Exception $e) {
        if ($e->getMessage() === 'Expired token') {
            $sql = 'DELETE FROM tokens WHERE token = :token';
            $stmt = $dbConn->prepare($sql);
            $stmt->bindParam(':token', $token, PDO::PARAM_STR);
            $stmt->execute();
        }
        echo json_encode(array('status' => '500', 'message' => 'Token Not Found or Expired', 'data' => []));
    }
} else {
    echo json_encode(array('status' => '500', 'message' => 'Bad request', 'data' => []));
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
        $mail->addAddress($email);
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
                            <p>Welcome to FPT POLYTECHNIC! Please verify your email address by clicking the button below:</p>
                            <p><a href="http://127.0.0.1:3456/verify.php?reset=' . $token . '" class="button">Verify Email</a></p>
                            <p>If you did not create an account with us, you can ignore this email.</p>
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
