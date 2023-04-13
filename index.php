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

$jsonFileData = file_get_contents('config.json');
$jsonFileArray = json_decode($jsonFileData, true);

$AvailibleTypesGET = $jsonFileArray['availableTypesGET'];
$AvailibleTypesPOST = $jsonFileArray['availableTypesPOST'];
$AvailibleTypesLINK = $jsonFileArray['availableTypesLINK'];


switch ($method) {
    case 'GET':
        GET_method($AvailibleTypesGET, $type, $connect, $id);
        break;
    case 'POST':
        POST_method($AvailibleTypesPOST, $type, $connect, $id);
        break;
    case 'LINK':
        LINK_method($AvailibleTypesLINK, $type, $connect, $id, $id2, $filter);
        break;
    case 'DELETE':
        DELETE_method($AvailibleTypesGET, $type, $connect, $id);
        break;
    case 'PATCH':
        PATCH_method($AvailibleTypesGET, $type, $connect, $id);
        break;
}



function GET_method($AvailibleTypes, $type, $connect, $id = null)
{

    checkType($type, $AvailibleTypes);

    // Converting type to DataBaseTableName
    $DataBaseTableName = UpSymbol($type);

    $sql = "SELECT * FROM $DataBaseTableName";

    if($type == 'converted_CurrentTasks' || $type == 'converted_CompletedTasks'){
        getConvertedTasks($type, $connect);
    } else if (isset($id)) {
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
    checkType($type, $AvailibleTypes);

    $DataBaseTableName = UpSymbol($type);

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



    addElement($DataBaseTableName, $data, $connect);
}

function LINK_method($AvailibleTypes, $type, $connect, $id, $id2, $filter)
{
    if (isset($id) && isset($id2) && isset($filter)) {
        addLink($connect, $id, $id2, $filter);
    } else {
        getErorrResponse(400, "required fields are missing. You must send task id, mover id and filter - current or completed link");
    }
}

function DELETE_method($AvailibleTypes, $type, $connect, $id)
{
    CheckType($type, $AvailibleTypes);

    $DataBaseTableName = UpSymbol($type);

    if (isset($id)) {
        deleteElement($DataBaseTableName, $id, $connect);
    } else {
        getErorrResponse(400, "ID is required");
    }

}

function PATCH_method($AvailibleTypes, $type, $connect, $id)
{
    checkType($type, $AvailibleTypes);

    $DataBaseTableName = UpSymbol($type);

    $data = file_get_contents('php://input');
    $data = json_decode($data, true);
    if (count($data) == 0) {
        getErorrResponse(400, "Data is empty. You must send data with JSON format. FormData doesn't work");
    }
    if (isset($id)) {
        patchElement($DataBaseTableName, $id, $data, $connect);
    } else {
        getErorrResponse(400, "ID is required");
    }
}