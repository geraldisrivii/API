<?php

// GET

function getDataFromID($sql, $connect, $errorMessage = 'Data not found')
{
    $result = mysqli_query($connect, $sql);
    verifyNotNull($result, $errorMessage);
    $user = mysqli_fetch_all($result, MYSQLI_ASSOC);
    http_response_code(200);
    echo json_encode($user);
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

    checkRequest([$name, $lastName, $password], "All fields are required");

    // Creating a query

    $sql = null;
    if ($type == 'managers') {
        $sql = "SELECT * FROM Managers WHERE `name` = '$name' AND `lastName` = '$lastName' AND `password` = '$password'";
    } elseif ($type == 'movers') {
        $sql = "SELECT * FROM movers WHERE `name` = '$name' AND `lastName` = '$lastName' AND `password` = '$password'";
    }

    $result = mysqli_query($connect, $sql);

    verifyNotNull($result, "User not found");

    // FETCHING DATA

    $people = mysqli_fetch_all($result, MYSQLI_ASSOC);

    echo json_encode($people[0]);

}