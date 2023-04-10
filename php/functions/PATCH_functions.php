<?php

function patchElement($dataBaseTableName, $id, $data, $connect)
{
    $sql = "SELECT * FROM `$dataBaseTableName` WHERE id = $id";

    $result = mysqli_query($connect, $sql);

    verifyNotNull($result, "Object that you atempt update isn't found");

    $sql = "UPDATE `$dataBaseTableName` SET ";
    foreach ($data as $key => $value) {
        $sql .= "`$key` = '$value', ";
    }

    $sql = substr($sql, 0, -2);

    $sql .= " WHERE id = $id";

    mysqli_query($connect, $sql);

    checkDataBaseRequest($connect, $sql);

    http_response_code(200);

    $response = [
        "status" => "success",
        "message" => "OBJECT UPDATED"
    ];

    echo json_encode($response);
}