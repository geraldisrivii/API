<?php

// GET
function getConvertedTasks($type, $connect)
{

    $Movers_Links = [];
    $DataBaseTableName = substr($type, strpos($type, '_') + 1);
    $sql = "SELECT * FROM $DataBaseTableName";

    $result = mysqli_query($connect, $sql);

    verifyNotNull($result, "dsd");

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
        $sql = "SELECT * FROM Tasks WHERE id = '$key'";
        $result = mysqli_query($connect, $sql);
        $task = mysqli_fetch_all($result, MYSQLI_ASSOC);
        $task = $task[0];
        // Get peoples
        $users = [];
        foreach ($value as $id) {
            $sql = "SELECT * FROM Movers WHERE id = '$id'";
            
            $result = mysqli_query($connect, $sql);
            
            verifyNotNull($result, "dsd");
            
            $user = mysqli_fetch_all($result, MYSQLI_ASSOC);

            $users[] = $user[0];
        }

        // Set peoples in task
        
        $task['peoples'] = $users;

        $tasks[] = $task;

        verifyNotNull($result, "dsd");
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