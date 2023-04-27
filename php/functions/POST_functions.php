<?php

// POST


function addElement($DataBaseTableName, $data, $connect)
{
    $sql = "INSERT INTO $DataBaseTableName (";
    foreach ($data as $key => $value) {
        $sql = $sql .  "$key, ";
    }
    $sql = substr($sql, 0, -2) . ") VALUES (";
    foreach ($data as $key => $value) {
        $sql = $sql .  "'$value', ";
    }
    $sql = substr($sql, 0, -2) . ")";
    
    mysqli_query($connect, $sql);

    checkDataBaseRequest($connect, $sql);

    http_response_code(201);

    $responseStr = $DataBaseTableName . substr(-2, 1);

    $response = [
        "status" => "success",
        "message" => "{$responseStr} added",
        "id" => mysqli_insert_id($connect)
    ];

    echo json_encode($response);

}
