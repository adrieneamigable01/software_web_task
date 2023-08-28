<?php
    require_once("connection.php");
    require_once  'default_response.php';
    
    $username = $_POST['username'];
    $password = $_POST['password'];
    $sql = "SELECT * FROM `users` WHERE `username`=? AND `password`=? ";
    $query = $conn->prepare($sql);
    $query->execute(array($username,$password));
    $row = $query->rowCount();
    $fetch = $query->fetch();
    if($row > 0) {
        displayJSON(array(
            'isError' => false,
            'message' => 'Login Successfuly',
            'data' => array( 
                'firstname'=>$fetch['firstname'], 
                'lastname'=>$fetch['lastname'], 
                'middlename'=>$fetch['middlename'], 
                'user_type'=>$fetch['user_type'], 
                'email'=>$fetch['email'], 
                'username'=>$fetch['username'], 
                'date_created'=>$fetch['date_created'], 
                'is_active'=>$fetch['is_active'], 
            ),
            'date' => date("Y-m-d"),
        ));
    } else{
        displayJSON(array(
            'isError' => true,
            'message' => 'Error Login',
            'data' => array(),
            'date' => date("Y-m-d"),
        ));
    }

?>