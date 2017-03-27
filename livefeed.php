<?php
@session_start();
include("includes/paths.php");
error_reporting(0);
if (isset($_POST['page'])) {
        $page = $_POST['page'];
}else{
	$page = 1;
}
$rp = $_POST['rp'];
require("includes/feeds.php");
$function = new feeds();
switch($_REQUEST['mode']){
    case 'market':
        echo $function->RecentMarketPosts($page,$rp);
        break;
    case 'live':
        echo $function->getRecentPosts($page, $rp, null);
        break;
    case 'sms':
        echo $function->RecentSMSPosts($page, null, null,'recent',$rp);
        break;
    case 'recent':
        echo $function->RecentActions($page,$rp);
        break;
	case 'myposts':
	    if(isset($_SESSION['sessionid'])){
			echo $function->getMyRecentPosts($page,$rp);
		}
		break;
}
exit;
