<?php
@session_start();
include("includes/paths.php");
error_reporting(1);

require("includes/ajax.class.php");
$function = new ajax();

if(empty($_REQUEST['mode'])){
	$arrValues = json_decode(file_get_contents('php://input'), true);
	$mode = $arrValues['mode'];
	$arrValues['id'] = intval($arrValues['id']);
}else{
	$arrValues = $_REQUEST;
	$arrValues['id'] = intval($arrValues['id']);
	$mode = $_REQUEST['mode'];
}


switch ($mode){
    case 'districts':
        if (isset($_POST['id'])) {
            $id = $_POST['id'];
            if(!is_array($id)){
                $id = array(intval($id));
            }
        }else{
                return;
        }
        echo  $function->getDistricts($id);
        break;
    case 'assemblies':
if (isset($_REQUEST['id'])) {
        	 $idsArr = explode(",", $_REQUEST['id']);
        	 foreach($idsArr AS $key=>$value){
        	 	 $id .= intval($value).',';
        	 }
            $id=substr($id, 0, -1);
            $arr = $function->getAssemblies($id);
           // echo  json_last_error_msg();
            echo json_encode($arr);
        }else{
            return;
        }
        break;
    case 'mobile':
        $id = $arrValues['security_id'];
        $resArr = null;
       
        if($arrValues['captcha'] == $_SESSION[$id]){
             $resArr['msg'] = $function->getContact($arrValues['id'], $arrValues['postType']);
             $resArr['error'] = false;
        }
        else{
            $resArr['msg'] = 'Captcha entered is Wrong';
            $rand = rand(15,456);
            $resArr['captcha']  = md5(time().$rand);
            $resArr['error'] = true;
        }
        //unset($_SESSION[$id]);
        $jsonStr = json_encode($resArr);
        echo $jsonStr;
        break;
}
exit;