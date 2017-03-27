<?php
@session_start();
include("includes/paths.php");
error_reporting(0);
require($includesPath . "userActions.php");
$function = new useractions();
include($includesPath . "template.class.php");
$template = new Template();
extract($_REQUEST);
$template->indexHtml("posts.html",array('grid','help'));


if(isset($_POST['postid']) && !empty($_POST['postid']) && $_POST['mode']==1){
    $y = $function->viewAllPostIdPosts($_POST['postid']);
}
else{
    if(empty($districtid) && empty($assemblyid) && empty( $categoryid) && empty($cropid) && empty($type)){
        $_SESSION['searchError'] = 'Please select atleast anyone of the fields(district,assembly,category,item and type).';
        header('location:index.php?mode=search');
    }
    $districtid = implode(",", $districtid);
    $assemblyid = implode(",", $assemblyid);
    $categoryid = implode(",", $categoryid);
    $cropid = implode(",", $cropid);
    $y = $function->viewAllPosts($districtid, $assemblyid, $categoryid, $cropid,$type);
    $resMode=(empty($districtid) && empty($cropid))?'':((empty($districtid) && !empty($cropid))?'crop':'district');
    $y1=($resMode=='')?'[]':$function->RecentSMSPosts(10,$districtid, $categoryid, $resMode);
}
$template->replace("jsonString", $y);


$template->replace("@Links@", "Search Results");
$template->replace("@sms_posts@",$y1);
unset($_REQUEST);
unset($_SESSION['error']);
$template->publish();