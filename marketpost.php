<?php
@session_start();
$comment = '';
error_reporting(1);
include("includes/paths.php");
require($includesPath . "market.class.php");
$function = new market();
include($includesPath . "template.class.php");
$template = new Template();
extract($_REQUEST);
extract($_SESSION);

if(!empty($_SESSION['LoginError']) && $_SESSION['LoginError']!=null){
    $comment .= "<script>window.alert({$_SESSION['LoginError']});</script>";
}

if ((eregi("<[^>]*script*\"?[^>]*>", $postid)) || (eregi("<[^>]*noscript*\"?[^>]*>", $postid)) ||
(eregi("<[^>]*XSS*\"?[^>]*>", $postid)) ||
(eregi("<[^>]*livescript*\"?[^>]*>", $postid))) {
	$template->marketHtml("notFound.html",array('help'));
	$template->replace('@notfound@','Post id should be numeric only');
	$template->publish();
	return;
}

$const = $constituency;
$name = (isset($name)) ? $name : 'notfound';
$y=null;
$y = $function->getPost($postid);

if(!empty($postid) && $postid!=null && $postid!='' && is_array($y)){
   $template->marketHtml("marketpost.html",array('socialmedia', 'help', 'marketnumber'));
} else{
   
    $template->marketHtml("notFound.html", array('help'));
     $template->replace('@notfound@', 'Post Not Found');
}
$arr1=array('@post_price@','@post_quantity@','@post_contact@','@post_location@','@post_date@','@post_views@','@subject_description@','@post_subject@',"@stock_status@","@post_mode@","@feedback@",'@cropid@','@link@','@posted_by@','@offer_price@','@stock_color@');//"@post_remaining@",
$template->ArrReplace($arr1, $y);

$imagesarr = $function->getImages($postid);
$videosarr = $function->getVideos($postid);
$function->db->runSQL("DELETE FROM recent_actions WHERE postid='".$postid."' AND actionid='10'");
$function->db->fnInsert("recent_actions","'','{$postid}','10',now(),'{$_SESSION['userid']}'");
$str_video_links = "";

if (count($imagesarr)>0) {
	foreach($imagesarr AS $id=>$Image){		
		$image_thumbnails .= "<a href='#' onclick=javascript:applyClass('$id')><img src='".$Image[1]."' alt='".$Image[0]."' width='80px' height='80px'  /></a>";
		$image_str .= '<img src="'.$Image[1].'" alt="'.$Image[0].'" id="img'.$id.'" height="515px" width="445px"'.$class.' />';
	}
	
} else {
    $image_str = '<img src="css/images/no-images.jpg" alt="no images available" width="449px" />';
}
if (count($videosarr)>0) {
	for($cou=0;$cou<count($videosarr);$cou++){
		
		list($ur,$code) = explode("=",$videosarr[$cou][1]);
		list($code1,$code2) = explode("&",$code);
		$coun = $cou+1;
		$str_video_links .= "[ <a href='#' onClick=videoClip('{$coun}')>Clip ({$coun}) </a>] ";
		$video_str .= '<iframe style="border:1px solid green;visibility:false;" name="frame'.$coun.'" id="frame'.$coun.'" border="1" title="YouTube video player" width="527" height="337" src="http://www.youtube.com/embed/'.$code1.'?wmode=transparent" frameborder="0" allowfullscreen=""></iframe>';
	}
	$video_str = '<p id="videoclip">'.$video_str.'</p>';
	
} else {
    $video_str = '<img src="css/images/no-video.jpg" alt="No Videos added!" height="337" width="527" />';
}
$vertical_list = $function->marketList('new',null,'vertical','limit 3');
$horizontal_list = $function->marketList('crop',$y[11],'horizontal','limit 3');
$msgTxt = ($y[9]=='Buying')?'Please Quote your Price along with your ':'';
$rplyImg = ($y[9]=='Buying')?'please_quote.png':'replayto-advertiser.png';

$rand = rand(15,456);
$salt = md5(time().$rand);
$arrReplace=array('@currentURL@', '@video_clip_links@', '@reply_error@', '@reply_post@', '@reply_name@', '@vertical_list@', '@horizontal_list@', '@video_clip@', '@images_thumbnails@', '@images_replace@','@msgTxt@','@rplyImg@', '@marketsalt@');
$arrReplaceWith=array($currentURL, $str_video_links, $_SESSION['LoginError'], $postid, $name, $vertical_list, $horizontal_list, $video_str, $image_thumbnails, $image_str, $msgTxt, $rplyImg, $salt);
$template->replace("jsonHeading", "");

$template->ArrReplace($arrReplace,$arrReplaceWith);

unset($_REQUEST);
unset($_SESSION['LoginError']);
$template->publish();