<?php
ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL); 
    require_once("connection.php");
    require_once  'default_response.php';
    
   
    $sql = "SELECT * FROM `enrollment` WHERE 1";
    $query1 = $conn->prepare($sql);
    $query1->execute();
    $allEnrollmentCount = $query1->rowCount();


    $pattern = '%' .date("Y-m-d"). '%';
    $sql2 = "SELECT * FROM `enrollment` WHERE `date_created`like :pattern";
    $query2 = $conn->prepare($sql2);
    $query2->execute([':pattern' => $pattern]);
    $currentEnrollmentCount = $query2->rowCount();


    $admission_pattern = '%Admission Admin%';
    $sql3 = "SELECT * FROM `users` WHERE `user_type`like :admission_pattern";
    $query3 = $conn->prepare($sql3);
    $query3->execute([':admission_pattern' => $admission_pattern]);
    $admissionCount = $query3->rowCount();

    $principal_pattern = '%Admission Admin%';
    $sql4 = "SELECT * FROM `users` WHERE `user_type` like :principal_pattern";
    $query4 = $conn->prepare($sql4);
    $query4->execute([':principal_pattern' => $principal_pattern]);
    $principalCount = $query4->rowCount();

    displayJSON(array(
        'isError' => false,
        'message' => 'Success',
        'data' => array( 
            'allEnrollmentCount'=>$allEnrollmentCount, 
            'currentEnrollmentCount'=>$currentEnrollmentCount,
            'admissionCount' => $admissionCount, 
            'principalCount' => $principalCount,
        ),
        'date' => date("Y-m-d"),
    ));

?>