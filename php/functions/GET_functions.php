<?php

// GET

function getDataFromID($sql, $connect, $errorMessage = 'Data not found')
{
    $result = mysqli_query($connect, $sql);
    $user = mysqli_fetch_all($result, MYSQLI_ASSOC);
    if (count($user) == 0) {
        http_response_code(404);
        $response = [
            "status" => "error",
            "message" => $errorMessage
        ];
        echo json_encode($response);
        die();
    }
    echo json_encode($user);
    return $user;
}

function getArray($sql, $connect)
{
    $result = mysqli_query($connect, $sql);
    $users = mysqli_fetch_all($result, MYSQLI_ASSOC);
    echo json_encode($users);
}
function getUserFromData($type, $data, $connect)
{
    // FIELDS

    $name = $data['name'];
    $lastName = $data['lastName'];
    $password = $data['password'];

    // VALIDATION (Checking if all fields are filled)

    if ($name === null || $lastName === null || $password === null) {
        http_response_code(400);
        $response = [
            "status" => "error",
            "message" => "All fields are required"
        ];
        echo json_encode($response);
        die();
    }

    // Creating a query

    $sql = '';
    if ($type == 'managers') {
        $sql = "SELECT * FROM Managers WHERE `name` = '$name' AND `last_name` = '$lastName' AND `password` = '$password'";
    } elseif ($type == 'movers') {
        $sql = "SELECT * FROM movers WHERE `name` = '$name' AND `last_name` = '$lastName' AND `password` = '$password'";
    }

    $result = mysqli_query($connect, $sql);

    // FETCHING DATA

    $people = mysqli_fetch_all($result, MYSQLI_ASSOC);
    if (count($people) == 0) {
        http_response_code(404);
        $response = [
            "status" => "error",
            "message" => "User not found"
        ];
        echo json_encode($response);
        die();
    }
    echo json_encode($people[0]);

}