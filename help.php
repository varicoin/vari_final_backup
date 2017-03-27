<?php
@session_start();
error_reporting(1);
include("includes/paths.php");
require($includesPath."user.class.php");
$function=new user();
extract($_REQUEST);
extract($_SESSION);
$userid=$_SESSION['userid'];

foreach ($_POST as $secvalue) {
    if ((eregi("<[^>]*script*\"?[^>]*>", $secvalue)) ||
    (eregi("<[^>]*noscript*\"?[^>]*>", $secvalue)) ||
    (eregi("<[^>]*XSS*\"?[^>]*>", $secvalue)) ||
    (eregi("<[^>]*livescript*\"?[^>]*>", $secvalue))) {
        $message = $userid.$secvalue;
	@mail('anjithkumar.garapati@gmail.com', 'hack help submit ', $message, $headers);
	@session_destory();
	@header("location:index.php");
    }
}

$helpcaptcha = isset($_POST['salt']);
if(empty($_POST['name']) || empty($_POST['email']) || empty($_POST['question']) || empty($_POST['details']) || empty($_POST['salt']) ){
       $arr = array('msg'=>'Required field(s) are missing name='.$_POST['name'].'&email='.$_POST['email'].'&question='.$_POST['question'].'&details='.$_POST['details'].'&salt='.$_POST['salt'],'error'=>true);
        echo json_encode($arr,true);
        exit;
}
if($helpcaptcha != $_SESSION[$_POST['salt']]){
	$arr = array('msg'=>'Captcha entered is invalid ','error'=>true);
    echo json_encode($arr);
    exit;	
}
$str="'','".htmlentities(strip_tags($name))."','".strip_tags($email)."','".htmlentities(strip_tags($question))."','".htmlentities(strip_tags($details))."','".htmlentities(strip_tags($phone))."',now(),0";

$postid = $function->insertAction("help",$str);

if($postid){
	/*$message = "Hi,<br> Got help request from ".$name."<br>Email: ".$email."<br>Question :".$question."<br>Details: ".$details."<br>Phone: ".$phone;*/
	$message = "Hello Test mail";
	$headers = 'From: <do-not-reply@vari.co.in>' . "\r\n";
   	$headers .= 'MIME-Version: 1.0' . "\r\n";
   	$headers .= 'Content-type: text/html; charset=iso-8859-1;' . "\r\n";
	/*  $headers = "MIME-Version: 1.0" . "\r\n";
	 $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
	 $headers.='From: Vari.co.in Help Desk <ram@vari.co.in>' . "\r\n";
	 $headers.='Reply-to: Vari.co.in <nikileshrolla@vari.co.in>' . "\r\n"; */
	 mail('nikileshrolla@vari.co.in','Help request from ', $message,$headers );
	
     $arr = array('msg'=>'Your query is received by us. We will contact you soon.','error'=>false);
     echo json_encode($arr);
}else{
        $arr = array('msg'=>'Some problem occurred please try again!','error'=>true);
        echo json_encode($arr);
        
}
exit;

