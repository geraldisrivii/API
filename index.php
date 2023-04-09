<?php

// Imports
require_once "php/functions.php";
require_once "php/connect.php";

// CORS headers
$origin = $_SERVER['HTTP_ORIGIN'];

header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST, GET, PATCH, DELETE, LINK");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Origin: $origin");

// CORS preflight request headers. 
// This is a security measure to prevent cross-site scripting attacks.


/* if($origin === null){
die(print_r('Origin is not set'));
} */

$method = $_SERVER['REQUEST_METHOD'];

$q = $_GET['q'];
$params = explode('/', $q);

$type = $params[0];
$id = $params[1];
$id2 = $params[2];
$filter = $params[3];

$connect = mysqli_connect("localhost", "root", "", "LogisticProjectBD");
switch ($method) {
    case 'GET':
        GET($type, $connect, $id);
        break;
    case 'POST':
        POST($type, $connect, $id);
}


function GET($type, $connect, $id = null)
{
    switch ($type) {
        case 'managers':
        case 'movers':
            if (isset($id)) {
                getUserFromID($type, $id, $connect);
            } elseif (count($_GET) > 1) {
                getUserFromData($type, $_GET, $connect);
            } else {
                getUsers($type, $connect);
            }
            break;
    }
}

function POST($type, $connect, $id)
{
    switch ($type) {
        case 'managers':
        case 'movers':
            if (isset($id)) {
                http_response_code(400);
                $response = [
                    "status" => "error",
                    "message" => "ID isn't required"
                ];
                echo json_encode($response);
            } else {
                addUser($type, $_POST, $connect);
            }
            break;
        case 'tasks':
            if (isset($id)) {
                http_response_code(400);
                $response = [
                    "status" => "error",
                    "message" => "ID isn't required"
                ];
                echo json_encode($response);
            } else {
                addTask($_POST, $connect);
            }

    }

}