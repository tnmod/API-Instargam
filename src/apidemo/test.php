<?php

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$a = $_GET['a'];
$b = $_GET['b'];
$c = $_GET['c'];

//
$delta = $b * $b - 4 * $a * $c;
if ($delta > 0) {
    echo json_encode(
        array(
            "X1" => ((- ($b) + sqrt($delta)) / (2 * $a)),
            "X2" => ((- ($b) - sqrt($delta)) / (2 * $a))
        )
    );
} else if ($delta == 0) {
    echo json_encode(
        array(
            "X" => (-$b / 2 * $a)
        )
    );
} else {
    echo json_encode(
        array(
            "Vonghiem"
        )
    );
}

$data = json_decode(file_get_contents("input"));

