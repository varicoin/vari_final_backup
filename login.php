<?php
@session_start();
error_reporting(1);
include("includes/paths.php");
$data['error_message'] = '';
extract($_POST);
extract($_SESSION);


if(isSet($_SESSION['sessionid']) && !empty($_SESSION['sessionid'])) {
    header('location:index.php');
}

if(isSet($_POST) && !empty($_POST)) {
    require("includes/function.class.php");
    $function=new functions();
    $password1=md5($password);
    $x = $function->checkUserlogin($email,$password1);
    $id=$email.time();
    if($x==true)
    {
        $_SESSION['sessionid']=md5($id);
        $_SESSION['email']=$email;
        if($email == 'sdand001@fiu.edu' || $email== 'anjithkumar.garapati@gmail.com') {
            $_SESSION['isAdmin'] = TRUE;
        }
        $function->setSessions($email);
        header('location:'.$_REQUEST['redirect']);
        exit;
    } else {
        $data['error_message'] = 'Invalid Login ceredentials!';
    }
}else{
	$data['error_message'] = 'Please login to view the page!';
}
if($data['error_message'] != '') {
	
    include($includesPath . "template.class.php");
    $template = new Template(true);
    $data['isBanner'] = 1;
    $template->indexHtml("login.html", $data);
    $template->publish();
}
