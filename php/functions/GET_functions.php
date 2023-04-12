<?php

// GET

function getDataFromID($sql, $connect, $errorMessage = 'Data not found')
{
    $result = mysqli_query($connect, $sql);
    verifyNotNull($result, $errorMessage);

    $user = mysqli_fetch_all($result, MYSQLI_ASSOC);
    echo json_encode($user[0]);
}

function getArray($sql, $connect)
{
    $result = mysqli_query($connect, $sql);
    $users = mysqli_fetch_all($result, MYSQLI_ASSOC);

    echo json_encode($users);
}

function getElementsFromData($type, $data, $connect)
{
    $dataTableName = null;
    if ($type == 'managers') {
        $dataTableName = 'Managers';
    } elseif ($type == 'movers') {
        $dataTableName = 'movers';
    } elseif ($type == 'currentTasks') {
        $dataTableName = 'CurrentTasks';
    } elseif ($type == 'completedTasks') {
        $dataTableName = 'CompletedTasks';
    }

    $sql = "SELECT * FROM $dataTableName WHERE ";

    array_shift($data);
    foreach ($data as $key => $value) {
        $sql = $sql . "`$key` = $value AND ";
    }

    $sql = substr($sql, 0, -5) . ";";

    $result = mysqli_query($connect, $sql);

    checkDataBaseRequest($connect, $sql);

    verifyNotNull($result, substr($type, 0, -1) . " not found");

    $user = mysqli_fetch_all($result, MYSQLI_ASSOC);

    echo json_encode($user);
}