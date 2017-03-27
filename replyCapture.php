<?php
@session_start();
ob_start();
error_reporting(0);
include("includes/paths.php");
require($includesPath . "function.class.php");
$function = new functions();
include($includesPath . "template.class.php");
$template = new Template();
$sent = false;
foreach ($_POST as $secvalue) {
    if ((eregi("<[^>]*script*\"?[^>]*>", $secvalue)) ||
    (eregi("<[^>]*object*\"?[^>]*>", $secvalue)) ||
    (eregi("<[^>]*iframe*\"?[^>]*>", $secvalue)) ||
    (eregi("<[^>]*applet*\"?[^>]*>", $secvalue)) ||
    (eregi("<[^>]*meta*\"?[^>]*>", $secvalue)) ||
    (eregi("<[^>]*style*\"?[^>]*>", $secvalue)) ||
    (eregi("<[^>]*form*\"?[^>]*>", $secvalue)) ||
    (eregi("<[^>]*img*\"?[^>]*>", $secvalue)) ||
    (eregi("<[^>]*noscript*\"?[^>]*>", $secvalue)) ||
    (eregi("<[^>]*applet*\"?[^>]*>", $secvalue)) ||
    (eregi("<[^>]*vbscript*\"?[^>]*>", $secvalue)) ||
    (eregi("<[^>]*embed*\"?[^>]*>", $secvalue)) ||
    (eregi("<[^>]*frame*\"?[^>]*>", $secvalue)) ||
    (eregi("<[^>]*style*\"?[^>]*>", $secvalue)) ||
    (eregi("<[^>]*frameset*\"?[^>]*>", $secvalue)) ||
    (eregi("<[^>]*html*\"?[^>]*>", $secvalue)) ||
    (eregi("<[^>]*body*\"?[^>]*>", $secvalue)) ||
    (eregi("<[^>]*!DOCTYPE*\"?[^>]*>", $secvalue)) ||
    (eregi("<[^>]*form*\"?[^>]*>", $secvalue)) ||
    (eregi("<[^>]*link*\"?[^>]*>", $secvalue)) ||
    (eregi("<[^>]*title*\"?[^>]*>", $secvalue)) ||
    (eregi("<[^>]*title*\"?[^>]*>", $secvalue)) ||
    (eregi("<[^>]*bgsound*\"?[^>]*>", $secvalue)) ||
    (eregi("<[^>]*layer*\"?[^>]*>", $secvalue)) ||
    (eregi("<[^>]*XSS*\"?[^>]*>", $secvalue)) ||
    (eregi("<[^>]*background*\"?[^>]*>", $secvalue)) ||
    (eregi("<[^>]*mocha*\"?[^>]*>", $secvalue)) ||
    (eregi("<[^>]*livescript*\"?[^>]*>", $secvalue)) ||
    (eregi("\([^>]*\"?[^)]*\)", $secvalue)) ||
    (eregi("\"", $secvalue))) {
    //die ("sorry bad data...");
	@mail('anjithkumar.garapati@gmail.com', 'hack', $userid,$headers);
	
$_SESSION['LoginError'] = 'Invalid Data posted!';

@header("location:marketpost.php?postid={$_POST['postid']}");
return;
    }
}

if (empty($_POST['txtPhone']) || empty($_POST['txtName']) || empty($_POST['txtMessage']) || empty($_POST['txtEmail'])) {
    $error = "All fields are required<br>";
} else if (strlen($_POST['txtName']) < 3 || strlen($_POST['txtName']) > 100) {
    $error.="Name must contain least of 3 and maximum of 100 characters<br>";
}  else if(!preg_match('/^[\w., ]+$/', $_POST['txtMessage'])) {
	$error.="Only alphabets, numbers,comma's and spaces are allowed in message<br>";
}else if (!preg_match('/^([a-zA-Z0-9])+([\.a-zA-Z0-9_-])*@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-]+)+/', $_POST['txtEmail'])){
         $error.="Invalid Email Format<br>";
}

if ($_POST['spamcode'] !== $_SESSION['security_code']) {
    $error.="Your Text did not match image<br>";
}
unset($_SESSION['security_code']);

if ($error == '') {
    switch($_POST['mode']){
        case 'smsreply':
            $type=15;//15
            $recenttype=15;
            $url = 'smspost.php?postid=';
            break;
        case 'postreply':
            $type=1;//1
            $recenttype=2;
            $url = 'post.php?postid=';
            break;
        case 'marketreply':
            $type=2;//2
            $recenttype=8;
            $url = 'smsmarketpostpost.php?postid=';
            break;
    }
		$function->db->runSQL("DELETE FROM recent_actions WHERE postid='".$postid."' AND actionid='{$recenttype}'");
         $sent = false;
		$function->insertAction("recent_actions","'','{$postid}','{$recenttype}',now(),'{$userid}'");
         $function->insertAction("replies","'','{$type}','{$sent}','{$_POST['postid']}','{$_POST['txtMessage']}','{$_POST['mode_minprice']}','{$_POST['mode_price']}','{$_POST['mode_quantity']}','{$_POST['txtName']}','{$_POST['txtEmail']}','{$_POST['txtPhone']}',now(),''");
         $error = 'Reply sent successfully';
}

$_SESSION['LoginError'] = $error;

header("location{$url}{$_POST['postid']}");
unset($_REQUEST);
$template->publish();
?>
