<?php

function deleteElement($type, $id, $connect)
{
    $sql = "DELETE FROM `Tasks` WHERE id = $id";

}
