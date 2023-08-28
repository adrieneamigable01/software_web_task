<?php
    require_once("third_party/Phpmailer/src/Exception.php");
    require_once("third_party/Phpmailer/src/OAuth.php");
    require_once("third_party/Phpmailer/src/PHPMailer.php");
    require_once("third_party/Phpmailer/src/POP3.php");
    require_once("third_party/Phpmailer/src/SMTP.php");

    function send($recipient,$subject,$body){

        $default = [
            'protocol'      =>'smtp',
            'smtp_host'     =>'smtp.ipage.com',
            'smtp_user'     =>'email-notification@doitcebu.com',
            'smtp_pass'     =>'Thequick!123',
            'smtp_port'     =>'465',
            'validate'      =>'true',
            'encrypt'       =>'ssl',
            'from_name'     =>'Barili Prime Lending Corp.',
            'from_email'    =>'email-notification@doitcebu.com',
            'reply'         =>'email-notification@doitcebu.com',
        ];
     
        $send = new \PHPMailer\PHPMailer\PHPMailer;
        

        // $send->SMTPDebug = 1; // Enable verbose debug output
        // $send->SMTPDebug = 2;
        $send->SMTPDebug = 0; 
        $send->isSMTP(); // Set mailer to use SMTP
        $send->Host = $default['smtp_host'];
        $send->SMTPAuth = true; // Enable SMTP authentication
        $send->Username = $default['smtp_user']; // SMTP username
        $send->Password = $default['smtp_pass']; // SMTP password
        $send->SMTPSecure = $default['encrypt']; // Enable TLS encryption, `ssl` also accepted
        $send->Port = $default['smtp_port'];
        $send->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );
        $send->isHTML(true);
        $send->AddReplyTo('email-notification@doitcebu.com');
        $send->setFrom('email-notification@doitcebu.com','No reply');
        $send->addAddress($recipient);
        $send->Subject = $subject.' '.date("F d, Y H:i:s");
        $send->Body = $body;
        $send->send();
    }
?>