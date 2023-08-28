<?php

    function displayJSON($data){
        if(isset($_SERVER['HTTP_USER_AGENT']) && strstr($_SERVER['HTTP_USER_AGENT'],"MSIE")){
            header('Content-Type: application/json');
        }
        else{
            header('Content-Type: application/json');
            header('Access-Control-Allow-Methods: GET, POST');
            header('Access-Control-Allow-Origin: *');
            header("Cache-Control: no-cache");
            header("Pragma: no-cache");
            echo json_encode($data);
        }
    
    }
?>