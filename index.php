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
$param2 = $params[1];
$param3 = $params[2];
$param4 = $params[3];

$connect = mysqli_connect("localhost", "root", "", "LogisticProjectBD");

$jsonFileData = file_get_contents('config.json');
$jsonFileArray = json_decode($jsonFileData, true);

$AvailibleTypesGET = $jsonFileArray['availableTypesGET'];
$AvailibleTypesPOST = $jsonFileArray['availableTypesPOST'];
$AvailibleTypesLINK = $jsonFileArray['availableTypesLINK'];


switch ($method) {
    case 'GET':
        GET_method($AvailibleTypesGET, $type, $connect, $param2, $param3, $param4);
        break;
    case 'POST':
        POST_method($AvailibleTypesPOST, $type, $connect, $param2);
        break;
    case 'LINK':
        LINK_method($AvailibleTypesLINK, $type, $connect, $param2, $param3, $param4);
        break;
    case 'DELETE':
        DELETE_method($AvailibleTypesGET, $type, $connect, $param2);
        break;
    case 'PATCH':
        PATCH_method($AvailibleTypesGET, $type, $connect, $param2);
        break;
}



function GET_method($AvailibleTypes, $type, $connect, $param2 = null)
{

    checkType($type, $AvailibleTypes);

    // Converting type to DataBaseTableName
    $DataBaseTableName = UpSymbol($type);

    $sql = "SELECT * FROM $DataBaseTableName";

    if($type == 'converted_CurrentTasks' || $type == 'converted_CompletedTasks'){
        getConvertedTasks($type, $connect, $_GET);
    } else if (isset($param2)) {
        $sql = $sql . " WHERE `id` = '$param2'";
        getDataFromID($sql, $connect);
    } elseif (count($_GET) > 1) {
        getElementsFromData($DataBaseTableName, $_GET, $connect);
    } else {
        getArray($sql, $connect);
    }
}

function POST_method($AvailibleTypes, $type, $connect, $param2)
{
    checkType($type, $AvailibleTypes);

    $DataBaseTableName = UpSymbol($type);

    $data = null;
    if (count($_POST) == 0) {
        $data = file_get_contents('php://input');
        $data = json_decode($data, true);
    } else {
        $data = $_POST;
    }
    if (isset($param2)) {
        getErorrResponse(400, "ID isn't required");
    }



    addElement($DataBaseTableName, $data, $connect);
}

function LINK_method($AvailibleTypes, $type, $connect, $param2, $param3, $param4)
{
    if (isset($param2) && isset($param3) && isset($param4)) {
        addLink($connect, $param2, $param3, $param4);
    } else {
        getErorrResponse(400, "required fields are missing. You must send task id, mover id and filter - current or completed link");
    }
}

function DELETE_method($AvailibleTypes, $type, $connect, $param2)
{
    CheckType($type, $AvailibleTypes);

    $DataBaseTableName = UpSymbol($type);

    if (isset($param2)) {
        deleteElement($DataBaseTableName, $param2, $connect);
    } else {
        getErorrResponse(400, "ID is required");
    }

}

function PATCH_method($AvailibleTypes, $type, $connect, $param2)
{
    checkType($type, $AvailibleTypes);

    $DataBaseTableName = UpSymbol($type);

    $data = file_get_contents('php://input');
    $data = json_decode($data, true);
    if (count($data) == 0) {
        getErorrResponse(400, "Data is empty. You must send data with JSON format. FormData doesn't work");
    }
    if (isset($param2)) {
        patchElement($DataBaseTableName, $param2, $data, $connect);
    } else {
        getErorrResponse(400, "ID is required");
    }
}