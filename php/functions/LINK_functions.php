<?php

// LINK 

function addLink($connect, $id, $id2, $filter)
{
    $result = mysqli_query($connect, "SELECT * FROM `Tasks` WHERE id = $id2");

    verifyNotNull($result, "Task that must be linked is not found");

    $result = mysqli_query($connect, "SELECT * FROM `movers` WHERE id = $id");

    verifyNotNull($result, "Mover that must be linked is not found");

    $sql = '';

    switch ($filter) {
        case 'current':
            $sql = "INSERT INTO `CurrentTasks` (`id`, `user_id`, `task_id`) VALUES (NULL, '$id', '$id2')";
            break;
        case 'completed':
            $sql = "INSERT INTO `CompleatedTasks` (`id`, `user_id`, `task_id`) VALUES (NULL, '$id', '$id2')";
            break;
    }

    if ($sql === '') {
        http_response_code(500);
        $response = [
            "status" => "error",
            "message" => "Unknown filter"
        ];
        echo json_encode($response);
        die();
    }

    mysqli_query($connect, $sql);

    if(mysqli_error($connect)) {
        http_response_code(500);
        $response = [
            "status" => "error",
            "message" => mysqli_error($connect),
            "sql" => $sql
        ];
        echo json_encode($response);
        die();
    }

    echo json_encode([
        "status" => "success",
        "message" => "Link added",
        "idInto-{$filter}Tasks" => mysqli_insert_id($connect)
    ]);
}