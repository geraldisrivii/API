<?php


function verifyNotNull($result, $errorMessage = "Object isn't found")
{
    if ($result->num_rows == 0) {
        http_response_code(404);
        $response = [
            "status" => "error",
            "message" => $errorMessage
        ];
        echo json_encode($response);
        die();
    }
}

function checkRequest($array, $errorMessage = "All fields are required")
{
    foreach ($array as $value) {
        if ($value === null) {
            http_response_code(400);
            $response = [
                "status" => "error",
                "message" => $errorMessage
            ];
            echo json_encode($response);
            die();
        }
    }

}

function checkDataBaseRequest($connect, $sql)
{
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
}

function getErorrResponse($responseCode, $errorMessage)
{
    http_response_code(400);
    $response = [
        "status" => "error",
        "message" => $errorMessage
    ];
    echo json_encode($response);
    die();
}