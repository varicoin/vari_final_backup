<?php
@session_start();
error_reporting(1);
include("includes/paths.php");
$_POST = json_decode(file_get_contents('php://input'), true);
if(isSet($_POST ) && !empty($_POST )) {
    
    $arrvalues = $_POST ;
    $x = false;
    require("includes/user.class.php");
    $userObj = new user();
    
    if($arrvalues['captcha'] != $_SESSION[$arrvalues['salt']]){
        $data['error'] = true;
        $data['errorMsg'][] = 'Captcha miss match';
    }
    
    $arr = $userObj->isDataExist($arrvalues['email'], $arrvalues['mobile']);
    if($arr[0]){
        $data['error'] = true;
        $data['errorMsg'][] = 'Email Already exists';
    }
    
    if($arr[1]){
        $data['error'] = true;
        $data['errorMsg'][] = 'Mobile number Already exists';
    }
    if(!$data['error']){
       
       $arrvalues['password'] = md5($arrvalues['password']);
       if($arrvalues['specialOffers']){
           $arrvalues['specialOffers'] = 1;
       }else{
           $arrvalues['specialOffers'] = 0;
       }
       $x = $userObj->userInsert($arrvalues); 
    
    if($x)
    {
        $_SESSION['sessionid']=md5($arrvalues['email'].time());
        $_SESSION['email']=$arrvalues['email'];
        if($arrvalues['email'] == 'sdand001@fiu.edu' || $arrvalues['email']== 'anjithkumar.garapati@gmail.com') {
            $_SESSION['isAdmin'] = TRUE;
        }
        $userObj->setSessions($arrvalues['email']);
        $data['error'] = false;
        $data['reload'] = true;
    } else {
        $data['error'] = true;
        $data['errorMsg'][] = 'Registeration failed';
    }
   }
   else{
   	
   }
}else{
    $data['error'] = true;
    $data['error_message'] = 'No data received';
}

echo json_encode($data);
exit;

/*
if($data['error']){
	include("includes/template.class.php");
	$templateDis = new Template();
	$arrResults = $_POST;
	$arrResults['errors'] = $data;
	$arrResults['isBanner'] = 0;
	$templateDis->indexHtml("register.html", $arrResults);
	$templateDis->publish();
}
*/