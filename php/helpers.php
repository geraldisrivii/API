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