<?php
    require_once("connection.php");
    require_once  'default_response.php';
    
 
    $sql = "SELECT * FROM `users` WHERE `is_active`=? ";
    $query = $conn->prepare($sql);
    $query->execute(array(1));
    $fetch = $query->fetchAll(PDO::FETCH_ASSOC);
    displayJSON(array(
        'isError' => false,
        'message' => 'success',
        'data' => $fetch,
        'date' => date("Y-m-d"),
    ));

?>