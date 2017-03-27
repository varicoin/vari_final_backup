<?php
@session_start();
include("includes/paths.php");
require($includesPath . "function.class.php");
$function = new functions();

$sent = false;
foreach ($_POST as $secvalue) {
    if ((eregi("<[^>]*script*\"?[^>]*>", $secvalue)) ||    
    (eregi("<[^>]*noscript*\"?[^>]*>", $secvalue)) ||
    (eregi("<[^>]*XSS*\"?[^>]*>", $secvalue)) ||
    (eregi("<[^>]*livescript*\"?[^>]*>", $secvalue))) {
        $message = "content is .session userid..{$_SESSION['userid']}..postid..{$_POST['postid']}...content..{$secvalue}........Message....{$_POST['txtMessage']}";
	@mail('anjithkumar.garapati@gmail.com', 'reply advisterer hack',  $message,$headers);
	$error = 'Reply Failed, please avoid urls or links or tags in the message! &nbsp;&nbsp;';
        $_SESSION['ReplyAdvistserError'] = $error;
       $extend = ($_POST['name']=='')?'':"&name={$_POST['name']}";
       header("location:post.php?postid={$_POST['postid']}{$extend}");
    }
}
if (empty($_POST['txtPhone']) || empty($_POST['txtName']) || empty($_POST['txtMessage']) || empty($_POST['txtEmail'])) {
    $error = "All fields are required<br>";
} else if (strlen($_POST['txtName']) < 3 || strlen($_POST['txtName']) > 100) {
    $error.="Name must contain least of 3 and maximum of 100 characters<br>";
} else if (strlen($_POST['txtPhone']) > 12 || strlen($_POST['txtPhone']) < 10) {
    $error.="Phone must contain least of 10 and maximum of 12 characters<br>";
} else if (!preg_match('/^([a-zA-Z0-9])+([\.a-zA-Z0-9_-])*@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-]+)+/', $_POST['txtEmail'])){
         $error.="Invalid Email Format<br>";
}

if ($_POST['smsreplysecurity'] !== $_SESSION['smsreplysecurity']) {
    $error.="Your Text did not match image<br>";
}
unset($_SESSION['smsreplysecurity']);

if ($error == '') {
       
        $function->db->runSQL("DELETE FROM recent_actions WHERE postid='".$_POST['postid']."' AND actionid='2'");
        $sent = false;
		$function->insertAction("recent_actions","'','{$_POST['postid']}','15',now(),'{$userid}'");
        $insertid = $function->insertAction("replies","'','15','{$sent}','{$_POST['postid']}','{$_POST['txtMessage']}','{$_POST['mode_minprice']}','{$_POST['mode_price']}','{$_POST['mode_quantity']}','{$_POST['txtName']}','{$_POST['txtEmail']}','{$_POST['txtPhone']}',now(),'{$_POST['postasad']}'");
        if($insertid){
		$Smsmessage="got reply for {$_POST['postid']} from {$_POST['txtName']} & No {$_POST['txtPhone']} {$_POST['txtMessage']} Min {$_POST['mode_minprice']} Max {$_POST['mode_price']} per {$_POST['mode_quantity']}";
		
		$smstrackstr = "null,'".$postid."','".$_POST['txtMessage']."','".addslashes($Smsmessage)."','',now(),now(),'0','3','2'";
		$smsposid=$function->insertAction("sendsms", $smstrackstr);
			$error = 'SMS is successfully sent!';
		}else{
			$error = 'SMS is sending failed!';
		}
}
$_SESSION['ReplySMSAdvistserError'] = $error;
$extend = ($_POST['name']=='')?'':"&name={$_POST['name']}";
header("location:smspost.php?postid={$_POST['postid']}");
unset($_REQUEST);