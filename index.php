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

$AvailibleTypes = [
    'managers',
    'movers',
    'tasks',
    'currentTasks',
    'completedTasks'
];



switch ($method) {
    case 'GET':
        GET_method($AvailibleTypes, $type, $connect, $id);
        break;
    case 'POST':
        POST_method($AvailibleTypes, $type, $connect, $id);
        break;
    case 'LINK':
        LINK_method($AvailibleTypes, $type, $connect, $id, $id2, $filter);
        break;
    case 'DELETE':
        DELETE_method($AvailibleTypes, $type, $connect, $id);
        break;
    case 'PATCH':
        PATCH_method($AvailibleTypes, $type, $connect, $id);
        break;
}



function GET_method($AvailibleTypes, $type, $connect, $id = null)
{

    checkType($type, $AvailibleTypes);

    // Converting type to DataBaseTableName
    $firstSymbol = substr($type, 0, 1);

    $firstSymbolCapital = mb_strtoupper($firstSymbol);

    $DataBaseTableName = $firstSymbolCapital . substr($type, 1);
    $sql = "SELECT * FROM $DataBaseTableName";


    if (isset($id)) {
        $sql = $sql . " WHERE `id` = '$id'";
        getDataFromID($sql, $connect);
    } elseif (count($_GET) > 1) {
        getElementsFromData($type, $_GET, $connect);
    } else {
        getArray($sql, $connect);
    }
}

function POST_method($AvailibleTypes, $type, $connect, $id)
{
    $data = null;
    if (count($_POST) == 0) {
        $data = file_get_contents('php://input');
        $data = json_decode($data, true);
    } else {
        $data = $_POST;
    }
    if (isset($id)) {
        getErorrResponse(400, "ID isn't required");
    }



    addUser($type, $data, $connect);
    addTask($data, $connect);
}

function LINK_method($AvailibleTypes, $type, $connect, $id, $id2, $filter)
{
    switch ($type) {
        case 'tasks':
            if (isset($id) && isset($id2) && isset($filter)) {
                addLink($connect, $id, $id2, $filter);
            } else {
                getErorrResponse(400, "required fields are missing. You must send task id, mover id and filter - current or completed link");
            }
            break;
    }
}

function DELETE_method($AvailibleTypes, $type, $connect, $id)
{
    if (isset($id)) {
        switch ($type) {
            case 'tasks':
                deleteElement("Tasks", $id, $connect);
                break;
            case 'currentTasks':
                deleteElement("CurrentTasks", $id, $connect);
                break;
            case 'completedTasks':
                deleteElement("CompletedTasks", $id, $connect);
                break;
            case 'movers':
                deleteElement("movers", $id, $connect);
                break;
            case 'managers':
                deleteElement("Managers", $id, $connect);
            default:
                getErorrResponse(400, "type isn't supported");
                break;
        }
    }
}

function PATCH_method($AvailibleTypes, $type, $connect, $id)
{

    $data = file_get_contents('php://input');
    $data = json_decode($data, true);
    if (isset($id)) {
        switch ($type) {
            case 'tasks':
                patchElement("Tasks", $id, $data, $connect);
                break;
            case 'currentTasks':
                patchElement("CurrentTasks", $id, $data, $connect);
                break;
            case 'completedTasks':
                patchElement("CompletedTasks", $id, $data, $connect);
                break;
            case 'movers':
                patchElement("movers", $id, $data, $connect);
                break;
            case 'managers':
                patchElement("Managers", $id, $data, $connect);
            default:
                getErorrResponse(400, "type isn't supported");
                break;
        }
    }
}