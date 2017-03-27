<?php
@session_start();
error_reporting(1);

include("includes/paths.php");
require($includesPath . "post.class.php");
$post=new post();
$arrEmails = array();
$str = '';
$strMsg = '';
$emails = '';
$headers = '';
$msg = '';
$linksmessage ='';

if(!isset($_SESSION['sessionid'])){
	header("login.php");
	exit;
}

$arr = $post->cleanData($_POST);
unset($_POST);
$arrErrors = $post->validateRequiredData($arr);

if(!empty($arrErrors)){	
	
	switch($arr['action']){
		    case 'update':
		    	$str="`quantityType`='".$arr['txtQtyType']."', `subject`='".$arr['subject']."',`message`='".$arr['message']."',`minprice`='".$arr['minprice']."',`price`='".$arr['price']."',`quantity`='".$arr['quantity']."',`location`='".$arr['location']."',`contact_info`='".$arr['mobile']."',`postingby`='".$arr['postingby']."',`mode`='".$arr['mode']."',editedon=now()";
		    	$dbErrors[] = $post->editRecord("posts",$str,"WHERE id='".$arr['postid']."' AND userid='".$_SESSION['userid']."'");
		    	
		    	$post->deleteRecord("recent_actions","WHERE postid='".$arr['postid']."' AND actionid='6'");
		    	$post->insertRecord("recent_actions","'','{$arr['postid']}','6',now(),'{$_SESSION['userid']}'");
		    	
		    	if(!empty($_FILES)){
		    		$post->imagesHandler($_FILES,$arr['postid'],'update', $arr['imgrem']);
		    	}
		    	header("location:post.php?postid=".$arr['postid']);
		    	exit;
			break;
			case 'insert':
				require_once('twitter/twitter.class.php');
				$consumerKey = '5lEoi5MbEklLPyODMNqPKw';
				$consumerSecret = 'TV94QVQxvS506xL879L17HG8k3c9YlAylraO78pdBg';
				$accessToken = '264651435-DQfaiOflnMn3fo0wSXWjcB1QOWfltvArUi0wJpgL';
				$accessTokenSecret = 'pfLu3dOJxvd1C75KnOTm5rcrJsYbFCNrbSldtEyaJtA';
				$districts = null;
				$assemblies = null;
				
				$twitter = new Twitter($consumerKey, $consumerSecret, $accessToken, $accessTokenSecret);
				$Arrpostid = array();

				for($i=0;$i<sizeof($arr['assemblyid']);$i++){
					list($district, $assembly) = explode("_", $arr['assemblyid'][$i]);
					
					$poststr="'','".intVal($_SESSION['userid'])."','".intVal($assembly)."','".intVal($district)."','".intVal($arr['categoryid'])."','".intVal($arr['cropid'])."','".strip_tags($arr['subject'])."','".strip_tags($arr['message'])."','".strip_tags($arr['minprice'])."','".strip_tags($arr['price'])."','".strip_tags($arr['txtQtyType'])."','".strip_tags($arr['quantity'])."','".strip_tags($arr['location'])."','".strip_tags($arr['mobile'])."',now(),now()".",'".intVal($arr['postingby'])."','".intVal($arr['mode'])."','5','1'";
					$postid = $post->insertRecord("posts",$poststr);
					
					if($postid){
						if(!empty($_FILES)){
							$post->imagesHandler($_FILES, $postid, 'insert', null);
						}
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
						$smsposid=$post->insertRecord("sendsms", $trackStr);
					}
				}
				
				header("location:myProfile.php");
			break;
		}
}
else{
    include("includes/template.class.php");
    $template = new Template();
    $data = $arr;
    $data['images'] = array();
    $data['insertType'] = $arr['action'];
    if($data['insertType']=='update'){
    	foreach ($arr['images'] AS $key=>$value){
    		$dummyArr = explode("_", $value);
    	    $data['images'][] = array('id'=>$dummyArr[0], 'name'=>$dummyArr[1], 'location'=>$dummyArr[2]);
    	}
    }
    $data['errors'] = $arrErrors;
    $data = json_encode($data);
    $template->indexHtml("postaddguest.html", $data);
    $template->publish();
}