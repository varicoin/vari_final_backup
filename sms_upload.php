<?php 
@session_start();
include("includes/paths.php");
error_reporting(0);

require($includesPath . "userActions.php");
$useraction = new useractions();
include($includesPath . "template.class.php");
$template = new Template();
include($includesPath . "sms.class.php");
require($includesPath . "post.class.php");
$smsMsg = new sms();
extract($_REQUEST);
extract($_SESSION);
 $post=new post();

$arrEmails = array();
$str = '';
$x = null;
$strMsg = '';
$emails = '';
$headers = '';
$msg = '';
if(isset($_SESSION['sessionid'])){
    if(isset($action) && $action=='insert'){
        $j=0;
        $k=0;
        $failedStrs = null;
        $strContent = file_get_contents($_FILES['sms']['tmp_name']);
        $arrList = explode("_", $strContent);
	
        for($i=0;$i<sizeof($arrList);$i++){
        	
            $arrFields = explode("@", $arrList[$i]);
            //number@message@Category@DISTRICT@mode
            
            $arrFields[3] = $useraction->getParentId( $arrFields[3], 'name' );
            $arrFields[2] = $useraction->Categoryid( $arrFields[2] );
			$cropid = $arrFields[2];
			
            $Arrpostid= $postid = $smsMsg->smsPostInsert($arrFields);
            if(!empty($postid)){
                $mode = ($arrFields[4]=='Buying')?1:0;
                $smsConfirmMessage='Your '.$arrFields[4].' SMS Ad posted in www.vari.co.in SMS ID '.$postid.'  "'.$arrFields[1].'" send SMS with Min/Max price and location(PIN) to 9441851726 to post SMS Ad';
		        $smsMsg->sendMessage($smsConfirmMessage,$arrFields[0]);

		       
               	$trackStr = "null,{$postid},'2', now(), now(),'-1'";
               	$post->insertRecord("mailtracktbl", $trackStr);
                $j++;
            }else{
                    $k++;
                    $failedStrs .= $arrList[$i].'<br>';
            }
            
        }
        $x = 'Total Number of posts successful '.$j;
        $x .= "Total Number of posts failed ".$k;
        $x .= "The strings that failed to insert ".$failedStrs;
        
        if(!is_null($failedStrs)){
            $headers = 'From: Vari.co.in <admin@vari.co.in>' . "\r\n";
            $headers .= 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
            $Emailmessage = 'Hi,<br> Following strings failed to insert in database<br>'.$failedStrs;
            mail('dandamudik@gmail.com', 'SMS post failed string', "\r\n" . $Emailmessage,$headers);
        }
        
    }	
    $template->indexHtml("sms_uploads.html", array('help'));
    $template->publish();
}
else{
     header("location:index.php");
}
