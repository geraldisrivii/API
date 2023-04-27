<?php

// GET
function getConvertedTasks($type, $connect, $data)
{
    $start = $data['with'];
    $end = $data['without'];
    $limit = $data['limit'];
    $offset = $data['offset'];

    $Movers_Links = [];
    $DataBaseTableName = substr($type, strpos($type, '_') + 1);
    $sql = "SELECT * FROM $DataBaseTableName ";

    $sql = $sql . "WHERE `timeCreated` BETWEEN '$start 00:00:00' AND '$end 23:59:59' ";
    
    $sql = $sql . "LIMIT " . ($limit + 1);

    $result = mysqli_query($connect, $sql);

    checkDataBaseRequest($connect, $sql);

    verifyNotNull($result, substr($DataBaseTableName, 0, -1) . " not found");

    // Current Tasks - Not Converted (Just links)

    $links = mysqli_fetch_all($result, MYSQLI_ASSOC);

    // Value here is array - element of array - links

    foreach ($links as $value) {
        if ($Movers_Links[$value['task_id']] == null) {
            $Movers_Links[$value['task_id']] = [$value['user_id']];
        } else {
            $Movers_Links[$value['task_id']][] = $value['user_id'];
        }
    }

    $tasks = [];

    foreach ($Movers_Links as $key => $value) {
        // GET task
        $sql = "SELECT Tasks.id, Tasks.title, Tasks.price, currentTasks.timeCreated 
        FROM Tasks 
            LEFT JOIN currentTasks ON Tasks.id = currentTasks.task_id  
        WHERE Tasks.id = '$key'";

        $result = mysqli_query($connect, $sql);
        checkDataBaseRequest($connect, $sql);
        $task = mysqli_fetch_all($result, MYSQLI_ASSOC);
        $task = $task[0];
        // Get peoples
        $users = [];
        foreach ($value as $id) {
            $sql = "SELECT * FROM Movers WHERE id = '$id'";

            $result = mysqli_query($connect, $sql);

            verifyNotNull($result);

            $user = mysqli_fetch_all($result, MYSQLI_ASSOC);

            $users[] = $user[0];
        }

        // Set peoples in task

        $task['peoples'] = $users;

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