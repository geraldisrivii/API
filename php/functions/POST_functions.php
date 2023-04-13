<?php

// POST


function addUser($type, $data, $connect)
{
    $name = $data['name'];
    $lastName = $data['lastName'];
    $password = $data['password'];
    $login = $data['login'];

    checkRequest([$name, $lastName, $password], "All fields are required");

    $sql = null;

    if ($type === 'managers') {
        $sql = "INSERT INTO Managers (`id`, `name`, `lastName`, `login`, `password`) VALUES (NULL, '$name', '$lastName', '$login', '$password')";
    } elseif ($type === 'movers') {
        $sql = "INSERT INTO Movers (`id`, `name`, `lastName`, `login`, `password`, `isEnabled`) VALUES (NULL, '$name', '$lastName', '$login', '$password', 0)";
    }

    mysqli_query($connect, $sql);

    checkDataBaseRequest($connect, $sql);

    http_response_code(201);

    $responseStr = $type . substr(-2, 1);

    $response = [
        "status" => "success",
        "message" => "{$responseStr} added"
    ];

    echo json_encode($response);

}


function AddTask($data, $connect)
{
    $title = $data['title'];
    $price = $data['price'];

    checkRequest([$title, $price], "All fields are required");

    mysqli_query($connect, "INSERT INTO Tasks (`id`, `title` , `price`) VALUES (NULL, '$title', '$price')");

    checkDataBaseRequest($connect, "INSERT INTO Tasks (`id`, `title` , `price`) VALUES (NULL,  '$title', '$price')");

    http_response_code(201);

    $response = [
        "status" => "success",
        "message" => "Task added",
        "id" => mysqli_insert_id($connect)
    ];

    echo json_encode($response);
}