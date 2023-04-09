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
                "message" => "Unknown filter"
            ];
            echo json_encode($response);
            die();
        }
    }

}