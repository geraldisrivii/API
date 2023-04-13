<?php

// LINK 

function addLink($connect, $id, $id2, $filter)
{
    $result = mysqli_query($connect, "SELECT * FROM `Tasks` WHERE id = $id");

    verifyNotNull($result, "Task that must be linked is not found");

    $result = mysqli_query($connect, "SELECT * FROM `movers` WHERE id = $id2");

    verifyNotNull($result, "Mover that must be linked is not found");

    $sql = null;

    switch ($filter) {
        case 'current':
            $sql = "INSERT INTO `CurrentTasks` (`id`, `user_id`, `task_id`) VALUES (NULL, '$id2', '$id')";
            break;
        case 'completed':
            $sql = "INSERT INTO `CompletedTasks` (`id`, `user_id`, `task_id`) VALUES (NULL, '$id2', '$id')";
            break;
    }

    checkRequest([$sql], "Unknown filter");

    mysqli_query($connect, $sql);

    checkDataBaseRequest($connect, $sql);

    echo json_encode([
        "status" => "success",
        "message" => "Link added",
        "idInto-{$filter}Tasks" => mysqli_insert_id($connect)
    ]);
}