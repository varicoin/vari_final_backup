<?php
@session_start();
include("includes/paths.php");
error_reporting(1);
if(isSet($_SESSION['isAdmin']) && $_SESSION['isAdmin']==true){
    include("includes/template.class.php");
    $templateDis = new Template();
    include("includes/admin.class.php");
    $admin = new Admin();
    $arr['classifiedPosts'] = $admin->getPostMails(1, 30, '', 0);
    $arr['smsPosts'] = $admin->getPostMails(1, 30, '', 2);
    $arr['replies'] = $admin->getReplyDetails(1, 30, null, 1);
    $arr['smsReplies'] = $admin->getReplyDetails(1, 30, null, 15);
    $templateDis = new Template();
   	$templateDis->indexHtml("adminlayout.html",$arr);
   	$templateDis->publish();
    unset($_REQUEST);
    exit;
} else {
    echo 'Not Authorized';
}