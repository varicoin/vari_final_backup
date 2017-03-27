<?php
@session_start();
error_reporting(1);
include("includes/paths.php");
$_POST = json_decode(file_get_contents('php://input'), true);
$error = null;
foreach ($_POST as $secvalue) {
	if ((eregi("<[^>]*script*\"?[^>]*>", $secvalue)) ||
			(eregi("<[^>]*noscript*\"?[^>]*>", $secvalue)) ||
			(eregi("<[^>]*XSS*\"?[^>]*>", $secvalue)) ||
			(eregi("<[^>]*livescript*\"?[^>]*>", $secvalue))) {
				$error = 'Reply Failed, please avoid urls or links or tags in the message! ';
			}
}

if (empty($_POST['txtMessage']) || empty($_POST['txtName']) || empty($_POST['MinPrice']) || empty($_POST['txtEmail']) || empty($_POST['MaxPrice'])) {
	$error .= "All fields are required ";
} else if (strlen($_POST['txtName']) < 3 || strlen($_POST['txtName']) > 100) {
	$error.="Name must contain least of 3 and maximum of 100 characters ";
} else if (strlen($_POST['txtPhone']) > 12 || strlen($_POST['txtPhone']) < 10) {
	$error.="Phone must contain least of 10 and maximum of 12 characters ";
} else if (!preg_match('/^([a-zA-Z0-9])+([\.a-zA-Z0-9_-])*@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-]+)+/', $_POST['txtEmail'])){
	$error.="Invalid Email Format ";
}

if(!empty($error)){
	$data['error'] = true;
	$data['error_message'] = $error;
	echo json_encode($data);
	exit;
}

if(isSet($_POST) && !empty($_POST )) {
    
    $arrValues = $_POST ;
    $x = false;
    require("includes/post.class.php");
    $postObj = new post();
    
    if($arrvalues['captcha'] != $_SESSION[$arrvalues['salt']]){
        $data['error'] = true;
        $data['errorMsg'][] = 'Captcha miss match';
    }
    $type = ($arrValues['type'] == 'Ad')?1:15;
    $arrValues['postasad'] = ($arrValues['postasad'])?1:0;
    $str = "null, $type, 0, {$arrValues['postid']}, '{$arrValues['txtMessage']}', {$arrValues['MinPrice']}, {$arrValues['MaxPrice']}, {$arrValues['txtQtyType']}, {$arrValues['Quantity']}, '{$arrValues['txtName']}', '{$arrValues['txtEmail']}', {$arrValues['txtPhone']}, '{$arrValues['Location']}', {$arrValues['postedBy']}, now(),'{$arrValues['postasad']}'";
    
    if($postObj->insertRecord('replies',$str)){
    	$actionid = ($arrValues['type'] == 'Ad')?2:16;
    	$rstr = "null,{$arrValues['postid']},{$actionid},now(),0";
    	$postObj->insertRecord('recent_actions', $rstr);
    	$data['error'] = false;
    	$data['error_message'] = 'Reply Submitted Successfully!!!';
    }else{
    	$data['error'] = true;
    	$data['error_message'] = 'Reply Submission Failed!!!';
    }
    
}else{
    $data['error'] = true;
    $data['error_message'] = 'No data received';
}

echo json_encode($data);
exit;