<?php
ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL); 
    require_once("connection.php");
    require_once  'email_sending.php';
    require_once  'default_response.php';
    
    
    $sql = "INSERT INTO enrollment (enrollment_id,
                                    student_id, 
                                    student_firstname,
                                    student_lastname,
                                    student_middlename,
                                    student_birthdate,
                                    parent_firstname,
                                    parent_lastname,
                                    parent_middlename,
                                    parent_mobile,
                                    parent_email,
                                    relationship
                                     ) VALUES ( 
                                    :enrollment_id, 
                                    :student_id, 
                                    :student_firstname,
                                    :student_lastname,
                                    :student_middlename,
                                    :student_birthdate,
                                    :parent_firstname,
                                    :parent_lastname,
                                    :parent_middlename,
                                    :parent_mobile,
                                    :parent_email,
                                    :relationship)";

    $pdo_statement = $conn->prepare( $sql );

    $id = gen_uuid();
        
    $result = $pdo_statement->execute( array( ':enrollment_id'=> $id,  
                                            ':student_id'=> '0000', 
                                            ':student_firstname'=>$_POST['student_firstname'], 
                                            ':student_lastname'=>$_POST['student_lastname'], 
                                            ':student_middlename'=>$_POST['student_middlename'], 
                                            ':student_birthdate'=>$_POST['student_birthdate'],
                                            ':parent_firstname'=>$_POST['parent_firstname'], 
                                            ':parent_lastname'=>$_POST['parent_lastname'], 
                                            ':parent_middlename'=>$_POST['parent_middlename'],
                                            ':parent_mobile'=>$_POST['parent_mobile'], 
                                            ':parent_email'=>$_POST['parent_email'], 
                                            ':relationship'=>$_POST['relationship'] ) );
    
                                         

            
    if (!empty($result) ){
 
        $last_id = $conn->lastInsertId();

        $student_id = date("Ymd").'-'.$last_id;

        //Todo : Update Student ID
        $sql = "UPDATE enrollment SET student_id=? WHERE id=?";
        $stmt= $conn->prepare($sql);
        $stmt->execute([$student_id,$id]);

        $data = array( 'enrollment_id'=> $id,  
                'student_id'=> $student_id, 
                'student_firstname'=>$_POST['student_firstname'], 
                'student_lastname'=>$_POST['student_lastname'], 
                'student_middlename'=>$_POST['student_middlename'], 
                'student_birthdate'=>$_POST['student_birthdate'],
                'parent_firstname'=>$_POST['parent_firstname'], 
                'parent_lastname'=>$_POST['parent_lastname'], 
                'parent_middlename'=>$_POST['parent_middlename'],
                'parent_mobile'=>$_POST['parent_mobile'], 
                'parent_email'=>$_POST['parent_email'], 
                'relationship'=>$_POST['relationship'] 
        );


        displayJSON(array(
            'isError' => false,
            'message' => 'Successfuly Enrolled Student',
            'data' => $data,
            'date' => date("Y-m-d"),
        ));

        //Todo : need to send parent and adminitration email 
        sendParentEmail($_POST['parent_email'], "Enrollment Notification",$data);
        //Todo : need to send admin and adminitration email 
        sendAdminitratorEmail('adrienecarreamigable01@gmail.com', "Enrollment Notification",$data);
       
    }else{
        $studentFullName = $_POST['student_lastname'].', '.$_POST['student_firstname'].' '.$_POST['student_middlename'];
        displayJSON(array(
            'isError' => true,
            'message' => 'Error Enrolling Student '.$studentFullName,
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

    function sendAdminitratorEmail($parent_email,$subject,$data){
        $studentFullName = $data['student_lastname'].', '.$data['student_firstname'].' '.$data['student_middlename'];
        $parentFullName  = $data['parent_lastname'].', '.$data['parent_firstname'].' '.$data['parent_middlename'];
        $salutation = '<p>Good day admin!</p>
        <p>'.ucwords($studentFullName).' is now enrolled</p>
        <br><br>
        <p>Student Information</p>';
        $table = "<table class='table table-sm' style='text-align:center' border='1'>
            <thead>
                <tr>
                    <th>LRN or Student ID</th>
                    <th>Name of Child</th>
                    <th>Birthday</th>
                    <th>Parent Name</th>
                    <th>Parent Contact Number</th>
                    <th>Parent Email Address</th>
                    <th>Parent Relationship</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>".ucwords($data['student_id'])."</td>
                    <td>".ucwords($studentFullName)."</td>
                    <td>".ucwords($data['student_birthdate'])."</td>
                    <td>".ucwords($parentFullName)."</td>
                    <td>".ucwords($data['parent_mobile'])."</td>
                    <td>".ucwords($data['parent_email'])."</td>
                    <td>".ucwords($data['relationship'])."</td>
                </tr>
            </tbody>
        </table>";
        $body = $table;
        $email_body = htmlEmailBody($salutation,$body);
        send($parent_email,$subject,$email_body);
    }

    function sendParentEmail($parent_email,$subject,$data){
        $studentFullName = $data['student_lastname'].', '.$data['student_firstname'].' '.$data['student_middlename'];
        $parentFullName  = $data['parent_lastname'].', '.$data['parent_firstname'].' '.$data['parent_middlename'];
        $salutation = '<p>Good day Mr/Mrs '.ucwords($parentFullName).'!</p>
                      <p>'.ucwords($studentFullName).' is now enrolled</p>
                      <br><br>
        <p>Student Information</p>';
        $table = "<table class='table table-sm' style='text-align:center' border='1'>
            <thead>
                <tr>
                    <th>LRN or Student ID</th>
                    <th>Name of Child</th>
                    <th>Birthday</th>
                    <th>Parent Name</th>
                    <th>Parent Contact Number</th>
                    <th>Parent Email Address</th>
                    <th>Parent Relationship</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                <td>".ucwords($data['student_id'])."</td>
                <td>".ucwords($studentFullName)."</td>
                <td>".ucwords($data['student_birthdate'])."</td>
                <td>".ucwords($parentFullName)."</td>
                <td>".ucwords($data['parent_mobile'])."</td>
                <td>".ucwords($data['parent_email'])."</td>
                <td>".ucwords($data['relationship'])."</td>
                </tr>
            </tbody>
        </table>";
        $body = $table;
        $email_body = htmlEmailBody($salutation,$body);
        send($parent_email,$subject,$email_body);
    }

    function htmlEmailBody($salutation,$email_body){
        $html ='
            <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                <html xmlns="http://www.w3.org/1999/xhtml">
                
                <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <title>A Simple Responsive HTML Email</title>
                <link rel="preconnect" href="https://fonts.gstatic.com">
                <link href="https://fonts.googleapis.com/css2?family=Snowburst+One&display=swap" rel="stylesheet">
                <style type="text/css">
                body {margin: 0; padding: 0; min-width: 100%!important;}
                img {height: auto;}
                .content {width: 100%; max-width: 1200px;}
                .header {padding: 40px 30px 20px 30px;}
                .innerpadding {padding: 30px 30px 30px 30px;}
                .borderbottom {border-bottom: 1px solid #f2eeed;}
                .subhead {font-size: 15px; color: #ffffff; ; letter-spacing: 10px;}
                .h1, .h2, .bodycopy {color: #153643; }
                .h1 {font-size: 33px; line-height: 38px; font-weight: bold;}
                .h2 {padding: 0 0 15px 0; font-size: 24px; line-height: 28px; font-weight: bold;}
                .bodycopy {font-size: 16px; line-height: 22px;}
                .button {text-align: center; font-size: 18px; font-weight: bold; padding: 0 30px 0 30px;}
                .button a {color: #ffffff; text-decoration: none;}
                .footer {padding: 20px 30px 15px 30px;}
                .footercopy { font-size: 14px; color: #ffffff;}
                .footercopy a {color: #ffffff; text-decoration: underline;}
                
                @media only screen and (max-width: 550px), screen and (max-device-width: 550px) {
                body[yahoo] .hide {display: none!important;}
                body[yahoo] .buttonwrapper {background-color: transparent!important;}
                body[yahoo] .button {padding: 0px!important;}
                body[yahoo] .button a {background-color: #e05443; padding: 15px 15px 13px!important;}
                body[yahoo] .unsubscribe {display: block; margin-top: 20px; padding: 10px 50px; background: #2f3942; border-radius: 5px; text-decoration: none!important; font-weight: bold;}
                }
                
                /*@media only screen and (min-device-width: 601px) {
                    .content {width: 600px !important;}
                    .col425 {width: 425px!important;}
                    .col380 {width: 380px!important;}
                    }*/
                

                    


                </style>
                </head>
                
                <body yahoo bgcolor="#f6f8f1">
                <table width="100%" bgcolor="#f6f8f1" border="0" cellpadding="0" cellspacing="0">
                <tr>
                <td>
                    <!--[if (gte mso 9)|(IE)]>
                    <table width="600" align="center" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                        <td>
                    <![endif]-->     
                    <table bgcolor="#ffffff" class="content" align="center" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td bgcolor="#c7d8a7" class="header">
                        <!--[if (gte mso 9)|(IE)]>
                            <table width="425" align="left" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td>
                        <![endif]-->
                        <table class="col425" align="left" border="0" cellpadding="0" cellspacing="0" style="width: 100%; max-width: 425px;">  
                            <tr>
                            <td height="70">
                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td class="subhead" style="padding: 0 0 0 3px;">
                                    NOTIFICATION
                                    </td>
                                </tr>
                                <tr>
                                    <td class="h1" style="padding: 5px 0 0 0;">
                                        STUDENT ENROLLMENT
                                    </td>
                                </tr>
                                </table>
                            </td>
                            </tr>
                        </table>
                        <!--[if (gte mso 9)|(IE)]>
                                </td>
                            </tr>
                        </table>
                        <![endif]-->
                        </td>
                    </tr>
                    <tr style="background: url("https://png.pngtree.com/thumb_back/fw800/background/20201012/pngtree-snowy-mountain-christmas-party-image_412987.jpg"); background-size:cover;background-repeat:no-repeat;background-position: center center;  ">
                        <td class="innerpadding borderbottom">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0" >
                            <tr>
                            </tr>
                            <tr>
                            <td class="bodycopy">
                                '.$salutation.$email_body.'
                            </td>
                            </tr>
                        </table>
                        </td>
                    </tr>
                   
                    <tr>
                        <td class="footer" bgcolor="#44525f">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                            <td align="center" class="footercopy">
                                &copy; <a href="https://schoolemail.com">https://schoolemail.com</a> 2020 <br/>
                                <a href="m.me/116367786862437" class="unsubscribe"><font color="#ffffff">Message us at</font></a> 
                                <span class="hide">SCHOOL NAME</span>
                            </td>
                            </tr>
                            <tr>
                            <td align="center" style="padding: 20px 0 0 0;">
                                <table border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td width="37" style="text-align: center; padding: 0 10px 0 10px;">
                                    <a href="m.me/116367786862437">
                                        FOOTER
                                    </a>
                                </tr>
                                </table>
                            </td>
                            </tr>
                        </table>
                        </td>
                    </tr>
                    </table>
                    <!--[if (gte mso 9)|(IE)]>
                        </td>
                        </tr>
                    </table>
                    <![endif]-->
                    </td>
                </tr>
                </table>
                </body>
                </html>
            ';
        return $html;
    }
?>