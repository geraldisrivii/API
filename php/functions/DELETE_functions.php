<?php

function deleteElement($dataBaseName, $id, $connect)
{

    $sql = "DELETE FROM `$dataBaseName` WHERE id = $id";

    mysqli_query($connect, $sql);

    checkDataBaseRequest($connect, $sql);

    http_response_code(200);

    $response = [
        "status" => "success",
        "message" => "Element was deleted"
    ];

    echo json_encode($response);

}