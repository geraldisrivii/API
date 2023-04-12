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
        GET_method($type, $connect, $id);
        break;
    case 'POST':
        POST_method($type, $connect, $id);
        break;
    case 'LINK':
        LINK_method($type, $connect, $id, $id2, $filter);
        break;
    case 'DELETE':
        DELETE_method($type, $connect, $id);
        break;
    case 'PATCH':
        PATCH_method($type, $connect, $id);
        break;
}


function GET_method($type, $connect, $id = null)
{
    switch ($type) {
        case 'managers':
        case 'movers':
            if (isset($id)) {
                $sql = '';
                if ($type == 'managers') {
                    $sql = "SELECT * FROM Managers WHERE id = $id";
                } elseif ($type == 'movers') {
                    $sql = "SELECT * FROM movers WHERE id = $id";
                }
                getDataFromID($sql, $connect);
            } elseif (count($_GET) > 1) {
                getElementsFromData($type, $_GET, $connect);
            } else {
                $sql = null;

                if ($type == 'managers') {
                    $sql = "SELECT * FROM Managers";
                } elseif ($type == 'movers') {
                    $sql = "SELECT * FROM movers";
                }
                getArray($sql, $connect);
            }
            break;
        case 'tasks':
            if (isset($id)) {
                getDataFromID("SELECT * FROM `Tasks` WHERE `id` = '$id'", $connect, 'Task with this id is not found');
            } else {
                getArray("SELECT * FROM `Tasks`", $connect);
            }
            break;
        case 'currentTasks':
            if (isset($id)) {
                getDataFromID("SELECT * FROM `CurrentTasks` WHERE `id` = '$id'", $connect, 'Current Task with this id is not found');
            } elseif (count($_GET) > 1) {
                getElementsFromData($type, $_GET, $connect);
            } else {
                getArray("SELECT * FROM `CurrentTasks`", $connect);
            }
            break;
        case 'completedTasks':
            if (isset($id)) {
                getDataFromID("SELECT * FROM `CompletedTasks` WHERE `id` = '$id'", $connect, 'Compleated Task with this id is not found');
            } elseif (count($_GET) > 1) {
                getElementsFromData($type, $_GET, $connect);
            } else {
                getArray("SELECT * FROM `CompletedTasks`", $connect);
            }
            break;
        default:
            getErorrResponse(400, "type isn't supported");
            break;
    }
}

function POST_method($type, $connect, $id)
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
    switch ($type) {
        case 'managers':
        case 'movers':
            addUser($type, $data, $connect);
            break;
        case 'tasks':
            addTask($data, $connect);
            break;
    }

}

function LINK_method($type, $connect, $id, $id2, $filter)
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

function DELETE_method($type, $connect, $id)
{
    if (isset($id)) {
        switch ($type) {
            case 'tasks':
                deleteElement("Tasks", $id, $connect);
                break;
            case 'current-tasks':
                deleteElement("CurrentTasks", $id, $connect);
                break;
            case 'compleated-tasks':
                deleteElement("CompleatedTasks", $id, $connect);
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

function PATCH_method($type, $connect, $id)
{

    $data = file_get_contents('php://input');
    $data = json_decode($data, true);
    if (isset($id)) {
        switch ($type) {
            case 'tasks':
                patchElement("Tasks", $id, $data, $connect);
                break;
            case 'current-tasks':
                patchElement("CurrentTasks", $id, $data, $connect);
                break;
            case 'compleated-tasks':
                patchElement("CompleatedTasks", $id, $data, $connect);
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