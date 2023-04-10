<?php

function patchElement($dataBaseTableName, $id, $data, $connect)
{
    $sql = "UPDATE `$dataBaseTableName` SET ";
    foreach ($data as $key => $value) {
        $sql .= "`$key` = '$value', ";
    }
    
    $sql = substr($sql, 0, -2);

    mysqli_query($connect, $sql);

    checkDataBaseRequest($connect, $sql);

    http_response_code(200);

    $response = [
        "status" => "success",
        "message" => "OBJECT UPDATED"
    ];

    echo json_encode($response);
}