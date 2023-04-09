<?php

// POST


function addUser($type, $data, $connect)
{
    checkRequest($data, "All fields are required");
    /* if ($data['name'] === null || $data['lastName'] === null || $data['password'] === null) {
        http_response_code(400);
        $response = [
            "status" => "error",
            "message" => "All fields are required"
        ];
        echo json_encode($response);
        die();
    } */

    $name = $data['name'];
    $lastName = $data['lastName'];
    $password = $data['password'];

    $sql = "";

    if ($type === 'managers') {
        $sql = "INSERT INTO Managers (`id`, `name`, `lastName`, `password`) VALUES (NULL, '$name', '$lastName', '$password')";
    } elseif ($type === 'movers') {
        $sql = "INSERT INTO movers (`id`, `name`, `lastName`, `password`, `isEnabled`) VALUES (NULL, '$name', '$lastName', '$password', 1)";
    }

    mysqli_query($connect, $sql);

    if (mysqli_error($connect)) {
        http_response_code(500);
        $response = [
            "status" => "error",
            "message" => mysqli_error($connect),
            "sql" => $sql
        ];
        echo json_encode($response);
        die();
    }
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
    if ($text === null) {
        http_response_code(400);
        $response = [
            "status" => "error",
            "message" => "All fields are required"
        ];
        echo json_encode($response);
    }

    mysqli_query($connect, "INSERT INTO Tasks (`id`, `text`) VALUES (NULL, '$text')");

    if (mysqli_error($connect)) {
        http_response_code(500);
        $response = [
            "status" => "error",
            "message" => mysqli_error($connect),
            "sql" => "INSERT INTO Tasks (`id`, `text`) VALUES (NULL, '$text')"
        ];
        echo json_encode($response);
        die();
    }

    http_response_code(201);

    $response = [
        "status" => "success",
        "message" => "Task added",
        "id" => mysqli_insert_id($connect)
    ];

    echo json_encode($response);
}