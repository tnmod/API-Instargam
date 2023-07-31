<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

require '../../vendor/autoload.php';


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

use \Firebase\JWT\JWT;


include_once '../database/connection.php';
$body = json_decode(file_get_contents('php://input'));

$email = $_POST["email"] ?? $body->email ?? null;
$username = $_POST["username"] ?? $body->username ?? null;
$password = $_POST["password"] ?? $body->password ?? null;
$full_name = $_POST["full_name"] ?? $body->full_name ?? null;
$avatar = $_POST["avatar"] ?? $body->avatar ?? null;
$bio = $_POST["bio"] ?? $body->bio ?? null;

if (isset($username) && isset($password) && isset($email)) {
    $stmt = $dbConn->prepare('SELECT email FROM users WHERE email = :email');
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        echo json_encode(array('status' => '409', 'message' => 'conflict', 'data' => []));
    } else {
        try {
            $secret_key = 'tokenemail';
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $expiration_time = time() + (60 * 60);
            $payload = array(
                "email" => $email,
                "token_type" => 0,
                "exp" => $expiration_time
            );

            $token = JWT::encode($payload, $secret_key, "HS256");

            $currentTimestamp = time();
            $created_at = date('Y-m-d H:i:s', $currentTimestamp);
            $stmt = $dbConn->prepare('INSERT INTO users(username, password, email, full_name, avatar, bio) VALUES(:username, :password, :email, :full_name, :avatar, :bio)');
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->bindParam(':password', $password_hash, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':full_name', $full_name, PDO::PARAM_STR);
            $stmt->bindParam(':avatar', $avatar, PDO::PARAM_STR);
            $stmt->bindParam(':bio', $bio, PDO::PARAM_STR);
            $stmt->execute();

            $user_id = $dbConn->lastInsertId();
            error_log($user_id);

            $sql = 'INSERT INTO tokens(token, created_at, user_id) VALUES(:token, :created_at, :user_id)';
            $stmt = $dbConn->prepare($sql);
            $stmt->bindParam(':token', $token, PDO::PARAM_STR);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':created_at', $created_at, PDO::PARAM_STR);
            $stmt->execute();

            $result = sendMail($email, $username, 'Verify your account', $token);
            echo json_encode(array('status' => '200', 'message' => 'OK', 'data' => $result));
        } catch (\Exception $e) {
            $user_id = $dbConn->lastInsertId();
            $sql = 'DELETE FROM users WHERE id = :id';
            $stmt = $dbConn->prepare($sql);
            $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
            $stmt->execute();

            echo json_encode(array('status' => '500', 'message' => $e->getMessage(), 'data' => []));
        }
    }
} else {
    echo json_encode(array('status' => '400', 'message' => 'bad request', 'data' => []));
}

function sendMail($email, $username, $title, $token)
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
        return 'Message has been sent';
    } catch (\PHPMailer\PHPMailer\Exception $e) {
        return "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
