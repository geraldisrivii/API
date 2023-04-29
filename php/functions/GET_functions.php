<?php

// GET
function getConvertedTasks($type, $connect, $data)
{
    $limit = $data['limit'];
    $start = $data['start'];
    $end = $data['end'];
    $sortType = $data['sortType'];

    $fieldName = null;

    switch ($type) {
        case 'converted_CurrentTasks':
            $fieldName = 'timeCreated';
            break;
        case 'converted_CompletedTasks':
            $fieldName = 'timeCompleted';
            break;
    }
    $DataBaseTableName = substr($type, strpos($type, '_') + 1);

    $sql2 = "CREATE TABLE {$DataBaseTableName}Sort SELECT * FROM $DataBaseTableName ";

    if ($start and $end) {
        $sql2 = $sql2 . "WHERE $fieldName BETWEEN '$start 00:00:00' AND '$end 23:59:59' ";
    }

    if ($sortType === null) {
        $sortType = 'DESC';
    }

    $sql2 = $sql2 . "ORDER BY $fieldName $sortType";


    $result2 = mysqli_query($connect, $sql2);
    checkDataBaseRequest($connect, $sql2);


    $sql = "SELECT `task_id` FROM {$DataBaseTableName}Sort GROUP BY `task_id` ";
    $sql = $sql . "LIMIT $limit ";

    $result = mysqli_query($connect, $sql);
    checkDataBaseRequest($connect, $sql);
    $currentTasks = mysqli_fetch_all($result, MYSQLI_ASSOC);
    $tasks = [];

    mysqli_query($connect, "DROP TABLE {$DataBaseTableName}Sort");

    foreach ($currentTasks as $value) {
        $resultUsers = mysqli_query($connect, "SELECT `user_id`, `$fieldName` FROM $DataBaseTableName WHERE `task_id` = '$value[task_id]'");
        $users = mysqli_fetch_all($resultUsers, MYSQLI_ASSOC);

        $ids = [];

        foreach ($users as $user) {
            $ids[] = $user['user_id'];
        }

        $ids = implode(',', $ids);

        $resultMovers = mysqli_query($connect, "SELECT * FROM Movers WHERE `id` IN ($ids)");

        CheckDataBaseRequest($connect, $resultMovers);

        $Movers = mysqli_fetch_all($resultMovers, MYSQLI_ASSOC);

        $resultTask = mysqli_query($connect, "SELECT * FROM Tasks WHERE `id` = '$value[task_id]'");
        $task = mysqli_fetch_all($resultTask, MYSQLI_ASSOC);
        $task = $task[0];
        $task['peoples'] = $Movers;
        $task['time'] = $user[$fieldName];
        $tasks[] = $task;
    }
    echo json_encode($tasks);
}
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

function getElementsFromData($DataBaseTableName, $data, $connect)
{
    $sql = "SELECT * FROM $DataBaseTableName WHERE ";

    array_shift($data);
    foreach ($data as $key => $value) {
        $sql = $sql . "`$key` = '$value' AND ";
    }

    $sql = substr($sql, 0, -4) . ";";

    $result = mysqli_query($connect, $sql);

    checkDataBaseRequest($connect, $sql);

    verifyNotNull($result, substr($DataBaseTableName, 0, -1) . " not found");

    $user = mysqli_fetch_all($result, MYSQLI_ASSOC);

    echo json_encode($user);
}