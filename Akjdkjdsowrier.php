<?php
@session_start();
error_reporting(1);
include("includes/paths.php");
$_POST = json_decode(file_get_contents('php://input'), true);

if(isSet($_POST) && !empty($_POST) && isSet($_SESSION['isAdmin'])) {
	require("includes/admin.class.php");
	$adminObj = new admin();
	$mailid = intval($_POST['mailid']);
	$postid = intval($_POST['postid']);
    if($_POST['condition']=='Accept'){
        $newccmails = array();
        $newccphones = array();
        $ccmails = explode(",", $_POST['ccmails']);
		
        $ccphones = $_POST['ccphones'];
		if($_POST['mode']=='ads'){
        $postRecord = $adminObj->getPost($postid);
		}
		 if($_POST['mode']=='sms'){
			   $postRecord = $adminObj->getSmsPost($postid);
		 }
        foreach($ccmails AS $key=>$value){
    	    if(!empty($value) && $value!=" "){
    		    $newccmails[] = $value;
    	    }
        }
   
        $ccmails = implode(",",$newccmails);
        $tomail = $ccmails[0];
        array_shift($ccmails);
        if($_POST['mode']=='ads'){
        	$message = "Hi<br> {$postRecord['postedBy']} has posted an {$postRecord['mode']} Ad under the item {$postRecord['item']}: {$postRecord['subject']}<br> in District {$postRecord['district_name']} and in the assembly constituency {$postRecord['assembly']} to view https://vari.co.in/post.php?postid={$postid}<br><br>";
        	$message .= "Download VARI mobile andriod app : https://play.google.com/store/apps/details?id=com.vari.in&hl=en<br><br>";
        	$message .= "Send SMS with Min/Max price and location(PIN) to 9441851726 to post SMS Ad<br><br>";
        	$message .= "Feedback: varicoin@yahoo.com<br><br>Regards<br><a href='https://vari.co.in'>vari.co.in</a>";
        }
      if($_POST['mode']=='sms'){
        	$message = "Hi,<br>  {$postRecord['mode']} SMS Ad posted under the item {$postRecord['item']}: {$postRecord['subject']}<br> in District {$postRecord['district_name']} to view https://vari.co.in/smspost.php?postid={$postid}<br><br>";
        	$message .= "Download VARI mobile andriod app : https://play.google.com/store/apps/details?id=com.vari.in&hl=en<br><br>";
        	$message .= "Send SMS with Min/Max price and location(PIN) to 9441851726 to post SMS Ad<br><br>";
        	$message .= "Feedback: varicoin@yahoo.com<br><br>Regards<br><a href='https://vari.co.in'>vari.co.in</a>";
        }
        $smsMessage = $_POST['mobileText'];
        $adminObj->sendSMS($smsMessage, $ccphones);
        $adminObj->sendMail($tomail, $postRecord['subject'], $message, $ccmails);
    }
    $data['isSent'] = true;
    $data['msg'] = $adminObj->updateStatus($_POST['mode'], $_POST['condition'], $mailid);
    $data['error'] = false;
    
}else{
    $data['error'] = true;
    $data['error_message'] = 'No data received';
}

echo json_encode($data);
exit;