<?php
@session_start();
error_reporting(1);
include("includes/paths.php");
$_POST = json_decode(file_get_contents('php://input'), true);

if(isSet($_POST) && !empty($_POST) && isSet($_SESSION['isAdmin'])) {
	require("includes/admin.class.php");
	$adminObj = new admin();
	$replyid = intval($_POST['replyid']);
	$postid = intval($_POST['postid']);
    if($_POST['condition']=='Accept'){
       
        $tomail = $_POST['ccmail'];
        $ccphone = $_POST['ccphone'];
        
        $replyRecord = $adminObj->getReplyRec($replyid, $_POST['mode']);
        
        if(!empty($tomail)){
        	
        	$message = "Hi<br> {$replyRecord['name']} replied to your post https://vari.co.in/post.php?postid={$postid}:{$replyRecord['message']} <br><br>";
        	$message .= "Download VARI mobile andriod app : https://play.google.com/store/apps/details?id=com.vari.in&hl=en<br><br>";
        	$message .= "Send SMS with Min/Max price and location(PIN) to 9441851726 to post SMS Ad<br><br>";
        	$message .= "Feedback: varicoin@yahoo.com<br><br>Regards<br><a href='https://vari.co.in'>vari.co.in</a>";
        	$adminObj->sendMail($tomail, "{$replyRecord['name']} replied to your post", $message);
        }
        
        $smsMessage = $_POST['mobileText'];
        $adminObj->sendSMS($smsMessage, $ccphone);
        
        /*$poststr="'','".intVal($_SESSION['userid'])."','".intVal($assembly)."','".intVal($district)."','".intVal($arr['categoryid'])."','".intVal($arr['cropid'])."','".strip_tags($arr['subject'])."','".strip_tags($arr['message'])."','".strip_tags($arr['minprice'])."','".strip_tags($arr['price'])."','".strip_tags($arr['txtQtyType'])."','".strip_tags($arr['quantity'])."','".strip_tags($arr['location'])."','".strip_tags($arr['mobile'])."',now(),now()".",'".intVal($arr['postingby'])."','".intVal($arr['mode'])."','5','1'";
        $postid = $post->insertRecord("posts",$poststr);
        if($postid){
        	try{
        		$msg = "{$arr['subject']} http://vari.co.in/post.php?postid=" . $postid.' #vari #agriculture';
        		$status = $twitter->send( $msg );
        		$post->insertRecord("recent_actions","'','{$postid}','1',now(),'{$_SESSION['userid']}'");
        	}
        	catch(exception $e){
        		//do nothing
        	}
        	$trackStr = "null,{$postid},'0', now(), now(),'-1'";
        	$mailposid=$post->insertRecord("mailtracktbl", $trackStr);
        	
        }
        */
    }
    $data['isSent'] = true;
    $data['msg'] = $adminObj->updateStatus('replies', $_POST['condition'], $replyid);
    $data['error'] = false;
    
}else{
    $data['error'] = true;
    $data['error_message'] = 'No data received';
}

echo json_encode($data);
exit;