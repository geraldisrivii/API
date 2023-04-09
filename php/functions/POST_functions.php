<?php

// POST


function addUser($type, $data, $connect)
{
    $name = $data['name'];
    $lastName = $data['lastName'];
    $password = $data['password'];

    checkRequest([$name, $lastName, $password], "All fields are required");

    $sql = null;

    if ($type === 'managers') {
        $sql = "INSERT INTO Managers (`id`, `name`, `lastName`, `password`) VALUES (NULL, '$name', '$lastName', '$password')";
    } elseif ($type === 'movers') {
        $sql = "INSERT INTO movers (`id`, `name`, `lastName`, `password`, `isEnabled`) VALUES (NULL, '$name', '$lastName', '$password', 1)";
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
    $text = $data['text'];

    checkRequest([$text], "All fields are required");

    mysqli_query($connect, "INSERT INTO Tasks (`id`, `text`) VALUES (NULL, '$text')");

    checkDataBaseRequest($connect, "INSERT INTO Tasks (`id`, `text`) VALUES (NULL, '$text')");

    http_response_code(201);

    $response = [
        "status" => "success",
        "message" => "Task added",
        "id" => mysqli_insert_id($connect)
    ];

    echo json_encode($response);
}