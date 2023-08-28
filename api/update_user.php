<?php
ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL); 
    require_once("connection.php");
    require_once  'email_sending.php';
    require_once  'default_response.php';

    $id             = $_POST['user_id'];
    $firstname      = $_POST['firstname'];
    $lastname       = $_POST['lastname'];
    $middlename     = $_POST['middlename'];
    $user_type      = $_POST['user_type'];
    $email          = $_POST['email'];


    
    $sql = "UPDATE users SET 
                firstname=?,
                lastname=?,
                middlename=?,
                user_type=?,
                email=?,
                username=?
                WHERE id=?";
    $stmt= $conn->prepare($sql);
    $result = $stmt->execute([$firstname,$lastname,$middlename,$user_type,$email,$email,$id]);

            
    if (!empty($result) ){
 
    

        displayJSON(array(
            'isError' => false,
            'message' => 'Successfuly update user',
            'date' => date("Y-m-d"),
        ));

       
    }else{
        displayJSON(array(
            'isError' => true,
            'message' => 'Error updateing user',
            'date' => date("Y-m-d"),
        ));
    }

    
    function gen_uuid() {
        return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            // 32 bits for "time_low"
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
    
            // 16 bits for "time_mid"
            mt_rand( 0, 0xffff ),
    
            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand( 0, 0x0fff ) | 0x4000,
    
            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand( 0, 0x3fff ) | 0x8000,
    
            // 48 bits for "node"
            mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
        );
    }

?>